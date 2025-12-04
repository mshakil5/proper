<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tag extends Model
{
    protected $fillable = ['name','slug','status','created_by','updated_by'];

    public function contents()
    {
        return $this->belongsToMany(Content::class, 'content_tag');
    }

    public function galleryContents()
    {
        return $this->belongsToMany(Content::class, 'content_tag')
                    ->where('type', 1)
                    ->where('status', 1);
    }

    public function blogContents()
    {
        return $this->belongsToMany(Content::class, 'content_tag')
                    ->where('type', 2)
                    ->where('status', 1);
    }

    public function eventContents()
    {
        return $this->belongsToMany(Content::class, 'content_tag')
                    ->where('type', 3)
                    ->where('status', 1);
    }

    public function newsContents()
    {
        return $this->belongsToMany(Content::class, 'content_tag')
                    ->where('type', 4)
                    ->where('status', 1);
    }
}
