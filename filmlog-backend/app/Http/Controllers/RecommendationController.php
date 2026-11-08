<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Movie;
use App\Services\GeminiRecommender;
use Illuminate\Http\JsonResponse;

class RecommendationController extends Controller
{
    public function recommend(Request $request, GeminiRecommender $gemini): JsonResponse
    {
        // Frontend sends {prompt, query, description} with same content
        // Backend reads `query` as per AGENTS.md specification
        $data = $request->validate([
            'prompt' => ['sometimes', 'string', 'max:500'],
            'query' => ['sometimes', 'string', 'max:500'],
            'description' => ['sometimes', 'string', 'max:500'],
        ]);

        // Use query as primary input (per AGENTS.md specification)
        $input = $data['query'] ?? $data['prompt'] ?? $data['description'] ?? null;

        if (!$input) {
            return response()->json(['message' => 'Permintaan tidak valid. Harap berikan prompt, query, atau description.'], 400);
        }

        try {
            $result = $gemini->recommend($input); 
        } catch (\Throwable $e) {
            report($e);
            \Log::error("Recommendation endpoint error: " . $e->getMessage());
            return response()->json(['message' => 'Layanan rekomendasi sedang bermasalah.'], 502);
        }

        // Check match_quality
        $matchQuality = $result['match_quality'];
        $movieId = $result['movie_id'];
        $reasoning = $result['reasoning'];

        // Handle "none" match quality
        if ($matchQuality === 'none') {
            return response()->json([
                'message' => 'Tidak ada film yang cocok di katalog.'
            ], 404);
        }

        // Verify the movie exists in database
        $movie = Movie::find($movieId);

        if (! $movie) {
            return response()->json([
                'message' => 'Tidak ada film yang cocok di katalog.'
            ], 404);
        }

        // Return response in the format expected by frontend
        return response()->json([
            'movie_id'    => $movie->id,
            'reasoning'   => $reasoning,
            'movie'       => $movie,
            'exact_match' => $matchQuality === 'exact',
        ]);
    }
}
