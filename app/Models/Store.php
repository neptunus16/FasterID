<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Store extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'store_name', 'address', 'phone', 'image', 'is_active',
    ];

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }

    // ── Relationships ─────────────────────────────────────────────────────────
    public function owner()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function menus()
    {
        return $this->hasMany(Menu::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }
}
