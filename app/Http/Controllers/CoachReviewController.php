<?php

namespace App\Http\Controllers;

use App\Models\Coach;
use App\Models\CoachReview;
use App\Models\Reservation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class CoachReviewController extends Controller
{
    /**
     * Get coach reviews with stats
     */
    public function getCoachReviews(Coach $coach)
    {
        $reviews = $coach->reviews()
            ->with('user')
            ->latest()
            ->get();

        // Calculate stats
        $stats = [
            'total' => $reviews->count(),
            'average' => $reviews->count() > 0 ? round($reviews->avg('rating'), 1) : 0,
            'distribution' => $this->getRatingDistribution($reviews),
        ];

        return response()->json([
            'reviews' => $reviews,
            'stats' => $stats,
        ]);
    }

    /**
     * Store a new coach review
     */
    public function store(Request $request, Coach $coach)
    {
        // Validate input
        $validated = $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'review' => 'nullable|string|max:500',
            'reservation_id' => 'nullable|integer',
        ]);

        $user = auth()->user();

        // Check if user has booked with this coach
        $hasBooked = Reservation::where('user_id', $user->id)
            ->where('coach_id', $coach->id)
            ->exists();

        if (!$hasBooked) {
            return response()->json([
                'error' => 'Anda harus pernah melakukan booking dengan coach ini untuk memberikan review.',
            ], 403);
        }

        // Optional: Check if reviewing specific reservation
        if ($request->filled('reservation_id')) {
            $reservation = Reservation::where('id', $validated['reservation_id'])
                ->where('user_id', $user->id)
                ->where('coach_id', $coach->id)
                ->first();

            if (!$reservation) {
                return response()->json([
                    'error' => 'Reservasi tidak ditemukan atau bukan milik Anda.',
                ], 404);
            }

            // Check if already reviewed this reservation
            $existingReview = CoachReview::where('reservation_id', $validated['reservation_id'])->first();
            if ($existingReview) {
                return response()->json([
                    'error' => 'Anda sudah memberikan review untuk booking ini.',
                ], 409);
            }

            $validated['reservation_id'] = $reservation->id;
        }

        // Create review
        $review = $user->coachReviews()->create([
            'coach_id' => $coach->id,
            'rating' => $validated['rating'],
            'review' => $validated['review'] ?? null,
            'reservation_id' => $validated['reservation_id'] ?? null,
        ]);

        return response()->json([
            'message' => 'Review berhasil ditambahkan!',
            'review' => $review->load('user'),
        ], 201);
    }

    /**
     * Get rating distribution (helper for stats)
     */
    private function getRatingDistribution($reviews)
    {
        $distribution = [1 => 0, 2 => 0, 3 => 0, 4 => 0, 5 => 0];

        foreach ($reviews as $review) {
            $distribution[$review->rating]++;
        }

        return $distribution;
    }

    /**
     * Delete own review (if feature is added later)
     */
    public function destroy(CoachReview $review)
    {
        // Only review owner can delete
        if ($review->user_id !== auth()->id()) {
            return response()->json([
                'error' => 'Anda tidak memiliki akses untuk menghapus review ini.',
            ], 403);
        }

        $review->delete();

        return response()->json([
            'message' => 'Review berhasil dihapus.',
        ]);
    }
}
