<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use SoftDeletes;

    protected $guarded = [];

    protected static function boot()
    {
      parent::boot();

      static::deleting(function ($model) {
        if (auth()->check()) {
          $model->deleted_by = auth()->id();
          $model->save();
        }
      });
    }

    public function features()
    {
        return $this->hasMany(ProductFeature::class);
    }

    public function faqs()
    {
        return $this->hasMany(ProductFaq::class);
    }

    public function clients()
    {
        return $this->hasMany(ProductClient::class);
    }

    public function process()
    {
        return $this->hasOne(ProductProcess::class, 'product_id');
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function productTag()
    {
        return $this->belongsTo(ProductTag::class);
    }
}
