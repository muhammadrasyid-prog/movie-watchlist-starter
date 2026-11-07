<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Movie extends Model
{
    protected $fillable = [
        'title', 'year', 'director', 'genres', 'rating', 'duration',
        'synopsis', 'poster_url', 'cast_members', 
    ];
}
