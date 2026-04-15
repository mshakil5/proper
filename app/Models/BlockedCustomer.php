<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BlockedCustomer extends Model
{
    protected $guarded = [];

    public function orders()
    {
        return $this->hasMany(BlockedCustomerOrder::class);
    }
}
