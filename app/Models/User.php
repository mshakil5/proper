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
        $lifetime = $this->userPoints()
            ->where('source', 'social_share')
            ->where('source_action', 'facebook')
            ->count();

        if ($lifetime >= 5) {
            return false;
        }

        $today = $this->userPoints()
            ->where('source', 'social_share')
            ->where('source_action', 'facebook')
            ->whereDate('created_at', today())
            ->count();

        return $today < 1;
    }

    public function facebookSharesLifetime()
    {
        return $this->userPoints()
            ->where('source','social_share')
            ->where('source_action','facebook')
            ->count();
    }

    public function facebookShareStatusText()
    {
        if ($this->facebookSharesToday() >= 1) {
            return "✅ You’ve already shared today.";
        }

        if ($this->facebookSharesLifetime() >= 5) {
            return "🚫 Lifetime sharing limit reached.";
        }

        return "Share and earn 10 points ({$this->facebookSharesToday()}/1 today, max 5 total)";
    }

    public function facebookSharesToday()
    {
        return $this->userPoints()
            ->where('source', 'social_share')
            ->where('source_action', 'facebook')
            ->whereDate('created_at', today())
            ->count();
    }

    public function tikTokSharesLifetime()
    {
        return $this->userPoints()
            ->where('source','social_share')
            ->where('source_action','tiktok')
            ->count();
    }

    public function tikTokSharesToday()
    {
        return $this->userPoints()
            ->where('source', 'social_share')
            ->where('source_action', 'tiktok')
            ->whereDate('created_at', today())
            ->count();
    }

    public function instagramSharesLifetime()
    {
        return $this->userPoints()
            ->where('source','social_share')
            ->where('source_action','instagram')
            ->count();
    }

    public function instagramSharesToday()
    {
        return $this->userPoints()
            ->where('source', 'social_share')
            ->where('source_action', 'instagram')
            ->whereDate('created_at', today())
            ->count();
    }

    public function canShareTikTokToday()
    {
        $lifetime = $this->tikTokSharesLifetime();

        if ($lifetime >= 5) {
            return false;
        }

        $today = $this->tikTokSharesToday();

        return $today < 1;
    }

    public function canShareInstagramToday()
    {
        $lifetime = $this->instagramSharesLifetime();

        if ($lifetime >= 5) {
            return false;
        }

        $today = $this->instagramSharesToday();

        return $today < 1;
    }

    public function tikTokShareStatusText()
    {
        if ($this->tikTokSharesToday() >= 1) {
            return "✅ You've already shared on TikTok today.";
        }

        if ($this->tikTokSharesLifetime() >= 5) {
            return "🚫 TikTok lifetime sharing limit reached.";
        }

        return "Share and earn 10 points ({$this->tikTokSharesToday()}/1 today, max 5 total)";
    }

    public function instagramShareStatusText()
    {
        if ($this->instagramSharesToday() >= 1) {
            return "✅ You've already shared on Instagram today.";
        }

        if ($this->instagramSharesLifetime() >= 5) {
            return "🚫 Instagram lifetime sharing limit reached.";
        }

        return "Share and earn 10 points ({$this->instagramSharesToday()}/1 today, max 5 total)";
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

    public function deliverySubscription()
    {
        return $this->hasOne(DeliverySubscription::class);
    }

    public function hasActiveDeliverySubscription(): bool
    {
        $subscription = $this->deliverySubscription()
            ->where('status', 'active')
            ->where('ends_at', '>=', now())
            ->first();
        
        return $subscription !== null;
    }

    public function deliverySubscriptionPayments()
    {
        return $this->hasManyThrough(DeliverySubscriptionPayment::class, DeliverySubscription::class);
    }

    public function getActiveDeliverySubscription()
    {
        return $this->deliverySubscription()
            ->where('status', 'active')
            ->where('ends_at', '>=', now())
            ->first();
    }

    public function getSubscriptionWarningMessage()
    {
        $subscription = $this->deliverySubscription()->first();
        
        if (!$subscription) {
            return null;
        }
        
        if (!$subscription->isActive()) {
            return [
                'type' => 'danger',
                'icon' => 'fas fa-times-circle',
                'title' => 'Subscription Expired',
                'message' => 'Your Free Delivery Pass has expired. Renew now to continue enjoying unlimited free delivery!',
                'action' => 'Renew Subscription'
            ];
        }
        
        $daysRemaining = (int) now()->diffInDays($subscription->ends_at, false);
        
        if ($daysRemaining <= 0) {
            return [
                'type' => 'danger',
                'icon' => 'fas fa-exclamation-circle',
                'title' => 'Subscription Expired',
                'message' => 'Your Free Delivery Pass expired. Renew now to get unlimited free delivery again!',
                'action' => 'Renew Now'
            ];
        } elseif ($daysRemaining <= 7) {
            return [
                'type' => 'warning',
                'icon' => 'fas fa-clock',
                'title' => 'Renewal Reminder',
                'message' => "Your Free Delivery Pass expires in {$daysRemaining} day" . ($daysRemaining !== 1 ? 's' : '') . ". Renew now to avoid paying delivery charges!",
                'action' => 'Renew Subscription'
            ];
        } elseif ($daysRemaining <= 14) {
            return [
                'type' => 'info',
                'icon' => 'fas fa-info-circle',
                'title' => 'Upcoming Renewal',
                'message' => "Your Free Delivery Pass expires in {$daysRemaining} days. Plan ahead and renew!",
                'action' => 'Renew Subscription'
            ];
        }
        
        return null;
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