<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Title extends Model
{
    use SoftDeletes;
 
    protected $fillable = ['title', 'language_iso_639_1'];

    function series()
    {
        return $this->belongsTo(Series::class);
    }
}
