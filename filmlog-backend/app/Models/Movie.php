<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Movie extends Model
{
    protected $fillable = [
        'title', 'year', 'director', 'genres', 'rating', 'duration',
        'synopsis', 'poster_url', 'cast_members', 
    ];

    public function watchlists(): HasMany
    {
        return $this->hasMany(Watchlist::class);
    }
}
