<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductTag extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'status',
        'created_by',
        'updated_by'
    ];

    protected $casts = [
        'status' => 'boolean'
    ];

    // Relationship with products
    public function products()
    {
        return $this->hasMany(Product::class);
    }

    // Relationship with user who created
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // Relationship with user who updated
    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}