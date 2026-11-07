<?php

namespace App\Http\Controllers;

use App\Models\Watched;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class WatchedController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $watched = Watched::query()
            ->where('user_id', $request->user()->id)
            ->with('movie')
            ->latest()
            ->get();

        return response()->json($watched);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'movie_id' => ['nullable', 'required_without:movieId', 'integer', 'exists:movies,id'],
            'movieId' => ['nullable', 'required_without:movie_id', 'integer', 'exists:movies,id'],
            'rating' => ['sometimes', 'nullable', 'integer', 'between:1,5'],
            'review' => ['sometimes', 'nullable', 'string', 'max:5000'],
        ]);

        $movieId = $data['movie_id'] ?? $data['movieId'];
        $attributes = array_intersect_key($data, ['rating' => true, 'review' => true]);

        $watched = Watched::updateOrCreate(
            [
                'user_id' => $request->user()->id,
                'movie_id' => $movieId,
            ],
            $attributes
        );

        $watched->load('movie');

        return response()->json($watched, $watched->wasRecentlyCreated ? 201 : 200);
    }
}
