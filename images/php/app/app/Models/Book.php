<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Book extends Model
{
    use SoftDeletes;

    protected $fillable = ['title', 'isbn_10', 'isbn_13', 'series_id'];

    function series(){
        return $this->belongsTo(Series::class);
    }

    function cover(){
        return $this->morphOne(Media::class, 'item');
    }
}
