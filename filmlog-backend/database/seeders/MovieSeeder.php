<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Movie;

class MovieSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $json = file_get_contents(base_path('../data/movies.json'));
        $movies = json_decode($json, true);

        foreach ($movies as $movie) {
            Movie::updateOrCreate(
                ['id' => $movie['id']],
                [
                    'title'     => $movie['title'],
                    'year'      => $movie['year'],
                    'director'  => $movie['director'],
                    'genres'    => implode(',', $movie['genres']),
                    'rating'    => $movie['rating'],
                    'duration'  => $movie['duration'],
                    'synopsis'  => $movie['synopsis'],
                    'poster_url'  => $movie['poster_url'],
                    'cast_members'  => implode(',', $movie['cast']),
                ]
            );
        }
    }
}
