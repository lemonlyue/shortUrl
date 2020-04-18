<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class ShortUrl extends Model
{
    public $table = 'short_url';

    public $timestamps = false;
    public $fillable = [
        'id', 'url'
    ];
}
