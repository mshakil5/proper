<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DeliverySubscriptionPayment extends Model
{
    protected $fillable = [
        'delivery_subscription_id',
        'amount',
        'status',
        'billing_month',
        'paid_at',
        'payment_ref'
    ];

    protected $casts = [
        'billing_month' => 'date',
        'paid_at' => 'datetime',
        'amount' => 'decimal:2'
    ];

    public function subscription(): BelongsTo
    {
        return $this->belongsTo(DeliverySubscription::class, 'delivery_subscription_id');
    }

    public function isPaid(): bool
    {
        return $this->status === 'paid';
    }

    public function isFailed(): bool
    {
        return $this->status === 'failed';
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }
}