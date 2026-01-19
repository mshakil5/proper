<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Coupon extends Model
{
    protected $casts = [
        'start_date' => 'date',
        'end_date'   => 'date',
    ];

    public function calculateDiscount($amount)
    {
        if ($this->discount_type === 'percent') {
            return ($amount * $this->discount_value) / 100;
        }
        return $this->discount_value;
    }

    public function scopeActive($query)
    {
        $today = now()->startOfDay();

        return $query->where('is_active', true)
                    ->where(function($q) use ($today) {
                        $q->whereNull('start_date')->orWhere('start_date', '<=', $today);
                    })
                    ->where(function($q) use ($today) {
                        $q->whereNull('end_date')->orWhere('end_date', '>=', $today);
                    })
                    ->where(function($q) {
                        $q->whereNull('max_uses')->orWhereColumn('used_count', '<', 'max_uses');
                    });
    }

    public function scopeByCouponType($query, $type)
    {
        return $query->where('coupon_type', $type);
    }
}