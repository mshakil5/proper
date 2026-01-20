<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $guarded = [];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function purchasedGiftCards()
    {
        return $this->hasMany(GiftCard::class, 'purchased_by');
    }

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

    public function canShareToday()
    {
        return $this->userPoints()
            ->where('source', 'social_share')
            ->where('source_action', 'facebook')
            ->whereDate('created_at', today())
            ->count() < 5;
    }

    public function facebookSharesToday()
    {
        return $this->userPoints()
            ->where('source', 'social_share')
            ->where('source_action', 'facebook')
            ->whereDate('created_at', today())
            ->count();
    }

    public function getReferralCount()
    {
        return $this->userPoints()
            ->where('source', 'referral')
            ->where('referrer_id', $this->id)
            ->count();
    }

    public function getReferralPoints()
    {
        return $this->userPoints()
            ->where('source', 'referral')
            ->selectRaw('SUM(point) as total')
            ->value('total') ?? 0;
    }

    public function referralHistory()
    {
        return User::where('referred_by', $this->id)
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();
    }

    public function referrer()
    {
        return $this->belongsTo(User::class, 'referred_by');
    }

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($user) {
            $user->referral_code = 'REF' . date('Y') . strtoupper(substr($user->name, 0, 4)) . str_pad($user->id ?? 0, 4, '0', STR_PAD_LEFT);
        });
    }
}