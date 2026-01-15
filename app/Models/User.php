<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $guarded = [];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    public function userPoints()
    {
        return $this->hasMany(UserPoint::class);
    }

    public function getAvailablePointsAttribute()
    {
        return $this->userPoints()
            ->selectRaw('SUM(point) as total')
            ->value('total') ?? 0;
    }

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
}