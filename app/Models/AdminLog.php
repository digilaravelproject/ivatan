<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $admin_id
 * @property string $action
 * @property string|null $target_type
 * @property int|null $target_id
 * @property array<array-key, mixed>|null $payload
 * @property string|null $ip_address
 * @property string|null $user_agent
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User $admin
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AdminLog newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AdminLog newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AdminLog query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AdminLog whereAction($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AdminLog whereAdminId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AdminLog whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AdminLog whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AdminLog whereIpAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AdminLog wherePayload($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AdminLog whereTargetId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AdminLog whereTargetType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AdminLog whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AdminLog whereUserAgent($value)
 * @mixin \Eloquent
 */
class AdminLog extends Model
{
    use HasFactory;
    protected $table = 'admin_logs';

    protected $fillable = [
        'admin_id',
        'action',
        'target_type',
        'target_id',
        'payload',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'payload' => 'array',
    ];

    public function admin()
    {
        return $this->belongsTo(User::class, 'admin_id');
    }
}
