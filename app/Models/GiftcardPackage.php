<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class GiftcardPackage extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'amount', 'description', 'is_active', 'image'];

    protected $casts = [
        'is_active' => 'boolean'
    ];
}