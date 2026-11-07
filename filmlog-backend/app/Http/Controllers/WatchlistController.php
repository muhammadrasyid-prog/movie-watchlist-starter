<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Movie;
use App\Models\Watchlist;
use Illuminate\Http\JsonResponse;

class WatchlistController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        $movies = Movie::query()
            ->whereHas('watchlists', function ($query) use ($request) {
                $query->where('user_id', $request->user()->id);
            })
            ->orderBy('title')
            ->get();
        return response()->json($movies);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $validatedData = $request->validate([
            'movie_id' => ['required', 'integer', 'exists:movies,id'],
            'movieId' => ['nullable', 'required_without:movie_id', 'integer', 'exists:movies,id'],
        ]);

        $movieId = $validatedData['movie_id'] ?? $validatedData['movieId'];

        $watchlist = Watchlist::create([
            'user_id' => $request->user()->id,
            'movie_id' => $validatedData['movie_id'],
            'added_at' => now(),
        ]);

        return response()->json([
            'message' => 'Movie added to watchlist.',
            'movie' => Movie::findOrFail($watchlist->movie_id),
        ], $watchlist->wasRecentlyCreated ? 201 : 200);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, int $movieId): JsonResponse
    {
                $deleted = Watchlist::query()
            ->where('user_id', $request->user()->id)
            ->where('movie_id', $movieId)
            ->delete();

        if (!$deleted) {
            return response()->json(['message' => 'Movie not found in watchlist.'], 404);
        }

        return response()->json(['message' => 'Movie removed from watchlist.']);
    }
}
