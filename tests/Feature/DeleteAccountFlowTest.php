<?php

uses(Illuminate\Foundation\Testing\DatabaseTransactions::class);

use App\Models\User;
use App\Notifications\GenericNotification;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Laravel\Sanctum\Sanctum;
use Spatie\Permission\Models\Role;

beforeEach(function () {
    $this->password = 'correct-password';
    Role::firstOrCreate(['name' => 'user']);
    Role::firstOrCreate(['name' => 'admin']);
    $this->user = User::factory()->create([
        'email'    => 'test@example.com',
        'password' => Hash::make($this->password),
    ]);
});

// --------------------------------------------------------------------------
//  User: Request Account Deletion
// --------------------------------------------------------------------------

test('authenticated user can request account deletion', function () {
    Sanctum::actingAs($this->user);

    $response = $this->postJson('/api/v1/auth/delete-account');

    $response->assertOk();
    $response->assertJson(['status' => true]);

    $this->user->refresh();
    expect($this->user->trashed())->toBeTrue();
});

test('delete account stores single reason as array', function () {
    Sanctum::actingAs($this->user);

    $response = $this->postJson('/api/v1/auth/delete-account', [
        'reason' => 'Not using this app',
    ]);

    $response->assertOk();

    $this->user->refresh();
    expect($this->user->deletion_reason)->toBe(['Not using this app']);
});

test('delete account stores multiple reasons as array', function () {
    Sanctum::actingAs($this->user);

    $response = $this->postJson('/api/v1/auth/delete-account', [
        'reason' => ['Not using this app', 'Privacy concerns', 'Too many notifications'],
    ]);

    $response->assertOk();

    $this->user->refresh();
    expect($this->user->deletion_reason)->toBe([
        'Not using this app',
        'Privacy concerns',
        'Too many notifications',
    ]);
});

test('delete account without reason stores null', function () {
    Sanctum::actingAs($this->user);

    $response = $this->postJson('/api/v1/auth/delete-account');

    $response->assertOk();

    $this->user->refresh();
    expect($this->user->deletion_reason)->toBeNull();
});

test('admin can see deletion reason in trashed users', function () {
    $admin = User::factory()->create();
    $admin->assignRole('admin');

    Sanctum::actingAs($this->user);
    $this->postJson('/api/v1/auth/delete-account', [
        'reason' => ['Privacy concerns', 'Creating new account'],
    ]);

    $response = $this->actingAs($admin)->get(route('admin.users.trashed'));

    $response->assertOk();
    $response->assertSee('Privacy concerns');
    $response->assertSee('Creating new account');
});

test('deleted user cannot login after account deletion', function () {
    Sanctum::actingAs($this->user);
    $this->postJson('/api/v1/auth/delete-account');

    $response = $this->postJson('/api/auth/login', [
        'email'    => 'test@example.com',
        'password' => $this->password,
    ]);

    $response->assertStatus(401);
});

test('deleted user tokens are revoked', function () {
    $token = $this->user->createToken('MyApp')->plainTextToken;

    Sanctum::actingAs($this->user);
    $this->postJson('/api/v1/auth/delete-account');

    $response = $this->withHeader('Authorization', "Bearer {$token}")
        ->getJson('/api/v1/auth/delete-account');

    expect(in_array($response->status(), [401, 500]))->toBeTrue();
});

test('unauthenticated user cannot request account deletion', function () {
    $response = $this->postJson('/api/v1/auth/delete-account');

    $response->assertStatus(401);
});

// --------------------------------------------------------------------------
//  User: Restore Account
// --------------------------------------------------------------------------

test('user can restore account within 30 day window', function () {
    Sanctum::actingAs($this->user);
    $this->postJson('/api/v1/auth/delete-account');

    $response = $this->postJson('/api/v1/auth/restore-account', [
        'email'    => 'test@example.com',
        'password' => $this->password,
    ]);

    $response->assertOk();
    $response->assertJson(['status' => true]);

    $this->user->refresh();
    expect($this->user->trashed())->toBeFalse();
});

test('user can login again after account is restored', function () {
    Sanctum::actingAs($this->user);
    $this->postJson('/api/v1/auth/delete-account');

    $this->postJson('/api/v1/auth/restore-account', [
        'email'    => 'test@example.com',
        'password' => $this->password,
    ]);

    $response = $this->postJson('/api/auth/login', [
        'email'    => 'test@example.com',
        'password' => $this->password,
    ]);

    $response->assertOk();
    $response->assertJsonStructure(['status', 'data' => ['user', 'token']]);
});

test('restore with wrong password fails', function () {
    Sanctum::actingAs($this->user);
    $this->postJson('/api/v1/auth/delete-account');

    $response = $this->postJson('/api/v1/auth/restore-account', [
        'email'    => 'test@example.com',
        'password' => 'wrong-password',
    ]);

    $response->assertStatus(500);
    $response->assertJson(['status' => false]);
});

test('restore with non-existent email fails', function () {
    $response = $this->postJson('/api/v1/auth/restore-account', [
        'email'    => 'nonexistent@example.com',
        'password' => $this->password,
    ]);

    $response->assertStatus(500);
    $response->assertJson(['status' => false]);
});

test('restore fails when account is not deleted', function () {
    $response = $this->postJson('/api/v1/auth/restore-account', [
        'email'    => 'test@example.com',
        'password' => $this->password,
    ]);

    $response->assertStatus(500);
    $response->assertJson(['status' => false]);
});

test('restore fails when 30 days have passed', function () {
    $this->user->delete();
    $this->user->deleted_at = now()->subDays(31);
    $this->user->save();

    $response = $this->postJson('/api/v1/auth/restore-account', [
        'email'    => 'test@example.com',
        'password' => $this->password,
    ]);

    $response->assertStatus(500);
    $response->assertJson(['status' => false]);
});

// --------------------------------------------------------------------------
//  Admin: View Soft-Deleted Users
// --------------------------------------------------------------------------

test('admin can view soft deleted users', function () {
    $admin = User::factory()->create();
    $admin->assignRole('admin');

    Sanctum::actingAs($this->user);
    $this->postJson('/api/v1/auth/delete-account');

    $response = $this->actingAs($admin)->get(route('admin.users.trashed'));

    $response->assertOk();
    $response->assertSee($this->user->email);
});

// --------------------------------------------------------------------------
//  Console: Purge Expired Accounts
// --------------------------------------------------------------------------

test('purge command deletes expired users', function () {
    $expiredUser = User::factory()->create();
    $expiredUser->delete();
    $expiredUser->deleted_at = now()->subDays(31);
    $expiredUser->save();

    $this->artisan('account:purge-expired')
        ->assertSuccessful();

    expect(User::withTrashed()->find($expiredUser->id))->toBeNull();
});

test('purge command skips recently deleted users', function () {
    $recentUser = User::factory()->create();
    $recentUser->delete();
    $recentUser->deleted_at = now()->subDays(1);
    $recentUser->save();

    $this->artisan('account:purge-expired')
        ->assertSuccessful();

    $recentUser = User::withTrashed()->find($recentUser->id);
    expect($recentUser)->not->toBeNull();
    expect($recentUser->trashed())->toBeTrue();
});

test('purge command cleans up polymorphic notification records', function () {
    $user = User::factory()->create();
    $user->notify(new GenericNotification('test', ['title' => 'Test']));
    $user->delete();
    $user->deleted_at = now()->subDays(31);
    $user->save();

    expect(
        \Illuminate\Support\Facades\DB::table('notifications')
            ->where('notifiable_id', $user->id)
            ->where('notifiable_type', User::class)
            ->exists()
    )->toBeTrue();

    $this->artisan('account:purge-expired')->assertSuccessful();

    expect(
        \Illuminate\Support\Facades\DB::table('notifications')
            ->where('notifiable_id', $user->id)
            ->where('notifiable_type', User::class)
            ->exists()
    )->toBeFalse();
});

test('purge command dry run does not delete users', function () {
    $expiredUser = User::factory()->create();
    $expiredUser->delete();
    $expiredUser->deleted_at = now()->subDays(31);
    $expiredUser->save();

    $this->artisan('account:purge-expired --dry-run')
        ->assertSuccessful();

    $expiredUser = User::withTrashed()->find($expiredUser->id);
    expect($expiredUser)->not->toBeNull();
    expect($expiredUser->trashed())->toBeTrue();
});
