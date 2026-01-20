<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DeliverySubscription extends Model
{
    protected $fillable = [
        'user_id',
        'amount',
        'status',
        'started_at',
        'ends_at',
        'last_billed_at'
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'ends_at' => 'datetime',
        'last_billed_at' => 'datetime',
        'amount' => 'decimal:2'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(DeliverySubscriptionPayment::class);
    }

    public function isActive(): bool
    {
        return $this->status === 'active' && $this->ends_at >= now();
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isExpired(): bool
    {
        return $this->status === 'expired' || $this->ends_at < now();
    }
}