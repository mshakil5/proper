<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class GiftCard extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'amount',
        'balance',
        'status',
        'is_active',
        'purchased_by',
        'order_id',
        'purchased_at',
        'sent_to_email',
        'sent_at',
        'redeemed_by',
        'redeemed_at',
        'expires_at'
    ];

    protected $casts = [
        'purchased_at' => 'datetime',
        'sent_at' => 'datetime',
        'redeemed_at' => 'datetime',
        'expires_at' => 'datetime',
        'is_active' => 'boolean'
    ];

    public function purchasedBy()
    {
        return $this->belongsTo(User::class, 'purchased_by');
    }

    public function redeemedBy()
    {
        return $this->belongsTo(User::class, 'redeemed_by');
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function isNew()
    {
        return $this->status === 'new';
    }

    public function isUsed()
    {
        return $this->status === 'used';
    }

    public function isExpired()
    {
        return $this->status === 'expired';
    }

    public function markAsUsed()
    {
        $this->update(['status' => 'used']);
    }

    public function markAsExpired()
    {
        $this->update(['status' => 'expired']);
    }
}