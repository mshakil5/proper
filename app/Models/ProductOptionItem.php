<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductOptionItem extends Model
{
    protected $fillable = [
        'product_option_id',
        'product_id',
        'override_price'
    ];

    protected $casts = [
        'override_price' => 'decimal:2'
    ];

    public function productOption()
    {
        return $this->belongsTo(ProductOption::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}