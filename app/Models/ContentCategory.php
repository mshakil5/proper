<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ContentCategory extends Model
{
    protected $guarded = [];

    public function contents()
    {
        return $this->hasMany(Content::class, 'category_id');
    }

    public function galleryContents()
    {
        return $this->hasMany(Content::class, 'category_id')
                    ->where('type', 1)
                    ->where('status', 1);
    }

    public function blogContents()
    {
        return $this->hasMany(Content::class, 'category_id')
                    ->where('type', 2)
                    ->where('status', 1);
    }

    public function eventContents()
    {
        return $this->hasMany(Content::class, 'category_id')
                    ->where('type', 3)
                    ->where('status', 1);
    }

    public function newsContents()
    {
        return $this->hasMany(Content::class, 'category_id')
                    ->where('type', 4)
                    ->where('status', 1);
    }
}
