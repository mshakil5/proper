<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Content extends Model
{
    protected $guarded = [];

    public function category()
    {
        return $this->belongsTo(ContentCategory::class,'category_id');
    }

    public function images()
    {
        return $this->hasMany(ContentImage::class,'content_id');
    }

    public function tags()
    {
        return $this->belongsToMany(Tag::class, 'content_tag');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class,'created_by');
    }

}