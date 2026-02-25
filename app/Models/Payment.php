<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $guarded = [];

    protected $casts = [
        'metadata' => 'array',
    ];

    public function getStatusBadgeAttribute()
    {
        return match ($this->status) {
            'completed' => 'success',
            'pending'   => 'warning',
            'failed', 'refunded' => 'danger',
            default     => 'secondary',
        };
    }

    public function order()
    {
        return $this->belongsTo(Order::class, 'reference_id', 'id')
            ->where('payment_type', 'order');
    }
}
