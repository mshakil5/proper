<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Plan extends Model
{
    protected $fillable = [
        'name', 'amount', 'is_recommended', 'status',
        'included_features', 'excluded_features',
        'created_by', 'updated_by'
    ];

    protected $casts = [
        'included_features' => 'array',
        'excluded_features' => 'array',
        'is_recommended' => 'boolean',
        'status' => 'boolean',
    ];
}
