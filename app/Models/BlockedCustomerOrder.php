<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BlockedCustomerOrder extends Model
{
    protected $table = 'blocked_customer_orders';
    
    protected $fillable = [
        'blocked_customer_id',
        'email',
        'phone',
        'order_data'
    ];

    protected $casts = [
        'order_data' => 'json',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    public function blockedCustomer()
    {
        return $this->belongsTo(BlockedCustomer::class);
    }
}