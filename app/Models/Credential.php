<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Credential extends Model
{
    protected $fillable = ['gateway', 'client_id', 'client_secret', 'mode'];
}