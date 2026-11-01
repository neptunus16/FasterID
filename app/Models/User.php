<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name', 'email', 'phone', 'address',
        'password', 'role', 'status',
        'is_online', 'driver_status',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
            'is_online'         => 'boolean',
        ];
    }

    // ── Role helpers ──────────────────────────────────────────────────────────
    public function isAdmin(): bool    { return $this->role === 'admin'; }
    public function isDriver(): bool   { return $this->role === 'driver'; }
    public function isCustomer(): bool { return $this->role === 'customer'; }
    public function isPending(): bool  { return $this->status === 'pending'; }
    public function isActive(): bool   { return $this->status === 'active'; }

    // ── Driver status helpers ─────────────────────────────────────────────────
    public function isOnlineActive(): bool   { return $this->driver_status === 'online'; }
    public function isResting(): bool        { return $this->driver_status === 'resting'; }
    public function isOfflineDriver(): bool  { return $this->driver_status === 'offline'; }

    /** Label human-readable untuk driver_status */
    public function driverStatusLabel(): string
    {
        return match ($this->driver_status) {
            'online'   => 'Aktif',
            'resting'  => 'Istirahat',
            'offline'  => 'Offline',
            default    => 'Tidak Diketahui',
        };
    }

    /** Badge color class (Tailwind) untuk driver_status */
    public function driverStatusColor(): string
    {
        return match ($this->driver_status) {
            'online'  => 'bg-green-100 text-green-700',
            'resting' => 'bg-yellow-100 text-yellow-700',
            'offline' => 'bg-gray-100 text-gray-500',
            default   => 'bg-gray-100 text-gray-500',
        };
    }

    /** Icon untuk driver_status */
    public function driverStatusIcon(): string
    {
        return match ($this->driver_status) {
            'online'  => '🟢',
            'resting' => '🟡',
            'offline' => '⚫',
            default   => '⚫',
        };
    }

    // ── Relationships ─────────────────────────────────────────────────────────
    public function store()
    {
        return $this->hasOne(Store::class, 'user_id');
    }

    public function ordersAsCustomer()
    {
        return $this->hasMany(Order::class, 'customer_id');
    }

    public function ordersAsDriver()
    {
        return $this->hasMany(Order::class, 'driver_id');
    }

    public function orders()
{
    return $this->hasMany(Order::class, 'customer_id');
}
}
