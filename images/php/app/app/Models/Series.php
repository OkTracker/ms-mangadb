<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Series extends Model
{
    use SoftDeletes;

    protected $fillable = ['authors'];

    function titles()
    {
        return $this->hasMany(Title::class);
    }

    function books()
    {
        return $this->hasMany(Book::class);
    }

    function cover()
    {
        return $this->morphOne(Media::class, 'item');
    }
}
