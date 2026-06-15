<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\InterestResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        $user = $this->resource;

        // Handle soft-deleted user (trashed) - show "Deleted User" placeholder
        if ($user->trashed()) {
            return [
                'id' => null,
                'uuid' => null,
                'username' => 'deleted_user',
                'name' => 'Deleted User',
                'email' => null,
                'phone' => null,
                'occupation' => null,
                'bio' => 'This account has been deleted.',
                'gender' => null,
                'date_of_birth' => null,
                'language_preference' => null,
                'is_seller' => false,
                'is_employer' => false,
                'is_verified' => false,
                'status' => 'deleted',
                'reputation_score' => 0,
                'followers_count' => 0,
                'following_count' => 0,
                'posts_count' => 0,
                'profile_photo_url' => 'https://ui-avatars.com/api/?name=Deleted+User&color=fff&background=999&size=128',
                'is_mine' => false,
                'is_following' => false,
                'is_follower' => false,
                'chat_id' => null,
                'interests' => [],
                'profiles' => [],
                'active_profile' => null,
                'profile_type' => 'personal',
                'created_at' => $user->created_at,
                'updated_at' => $user->updated_at,
                'is_deleted_user' => true,
            ];
        }

        $currentUser = $request->user('sanctum');

        // Logic: Agar ye user ka apna data h toh full dikhao.
        // Login/Register response me currentUser null hota h, isliye hum resource check krte hain.
        $isMine = ($currentUser && $currentUser->id === $user->id) || ($user->is_own_profile ?? false);

        $activeProfile = $this->relationLoaded('activeProfile')
            ? $this->getRelation('activeProfile')
            : ($this->relationLoaded('profiles') ? $this->getRelation('profiles')->firstWhere('is_active', true) : $this->activeProfile);

        $profileType = 'personal';
        if ($activeProfile) {
            $profileType = $activeProfile->type === 'seller' ? 'ecommerce' : $activeProfile->type;
        }

        $data = [
            'id' => $this->id,
            'uuid' => $this->uuid,
            'username' => $this->username,
            'name' => $this->name,
            'email' => $this->getEmail($isMine),
            'phone' => $this->getPhone($isMine),
            'occupation' => $this->occupation,
            'bio' => $this->bio,
            'gender' => $this->gender,
            'date_of_birth' => $this->date_of_birth,
            'language_preference' => $this->language_preference,
            'is_seller' => (bool) $this->is_seller,
            'is_employer' => (bool) $this->is_employer,
            'is_verified' => (bool) $this->is_verified,
            'status' => $this->status,
            'reputation_score' => (int) $this->reputation_score,
            'followers_count' => (int) $this->followers_count,
            'following_count' => (int) $this->following_count,
            'posts_count' => (int) $this->posts_count,
            'profile_photo_url' => $this->profile_photo_url,
            'is_mine' => $this->when(isset($this->is_mine), $this->is_mine),
            'is_following' => $this->when(isset($this->is_following), $this->is_following),
            'is_follower' => $this->when(isset($this->is_follower), $this->is_follower),
            'chat_id' => $this->when(isset($this->chat_id), $this->chat_id),
            'interests' => $this->when($this->relationLoaded('interests'), function() {
                return InterestResource::collection($this->getRelation('interests'));
            }),
            'profiles' => $this->when($this->relationLoaded('profiles'), function() {
                return \App\Http\Resources\Profile\ProfileResource::collection($this->getRelation('profiles'));
            }),
            'active_profile' => $this->when($this->relationLoaded('profiles'), function() {
                $active = $this->getRelation('profiles')->firstWhere('is_active', true);
                return $active ? new \App\Http\Resources\Profile\ProfileResource($active) : null;
            }),
            'profile_type' => $profileType,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];

        return $data;
    }

    private function getEmail(bool $isMine): ?string
    {
        if ($isMine || !$this->hide_email) {
            return $this->email;
        }
        return $this->maskEmail($this->email);
    }

    private function getPhone(bool $isMine): ?string
    {
        if ($isMine || !$this->hide_phone) {
            return $this->phone;
        }
        return $this->maskPhone($this->phone);
    }

    private function maskEmail(?string $email): ?string
    {
        if (!$email) return null;
        $parts = explode("@", $email);
        $name = $parts[0];
        $domain = $parts[1] ?? '';
        $len = strlen($name);
        if ($len <= 2) return str_repeat("*", $len) . "@" . $domain;
        return substr($name, 0, 1) . str_repeat("*", $len - 2) . substr($name, -1) . "@" . $domain;
    }

    private function maskPhone(?string $phone): ?string
    {
        if (!$phone) return null;
        $len = strlen($phone);
        if ($len <= 2) return str_repeat("*", $len);

        // Only show last 2 digits
        return str_repeat("*", $len - 2) . substr($phone, -2);
    }
}
