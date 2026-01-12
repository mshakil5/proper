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
}
