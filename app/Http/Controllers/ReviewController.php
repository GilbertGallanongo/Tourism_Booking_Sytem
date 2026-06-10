<?php

namespace App\Http\Controllers;

use App\Models\Review;
use App\Models\TourPackage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ReviewController extends Controller
{
    public function store(Request $request, TourPackage $tourPackage): JsonResponse|RedirectResponse
    {
        $user = $request->user();

        abort_unless($user && $user->isTourist(), 403, 'Only tourists may submit reviews.');

        $validated = $request->validate([
            'rating' => ['required', 'integer', 'between:1,5'],
            'comment' => ['required', 'string', 'max:1000'],
        ]);

        $review = Review::create([
            'user_id' => $request->user()->id,
            'tour_package_id' => $tourPackage->id,
            'rating' => $validated['rating'],
            'comment' => $validated['comment'],
        ]);

        if ($request->expectsJson()) {
            return response()->json(['data' => $review->load('user')], 201);
        }

        return redirect()->route('packages.show', $tourPackage)
            ->with('success', 'Review submitted successfully.');
    }

    public function destroy(Review $review): JsonResponse|RedirectResponse
    {
        abort_unless(request()->user()?->id === $review->user_id, 403);

        $tourPackage = $review->tourPackage;
        $review->delete();

        if (request()->expectsJson()) {
            return response()->json(['message' => 'Review deleted successfully.']);
        }

        return redirect()->route('packages.show', $tourPackage)
            ->with('success', 'Review deleted successfully.');
    }
}
