<?php

namespace App\Http\Controllers;

use App\Models\Feedback;
use App\Models\CoachReview;
use App\Models\Reservation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class ReviewController extends Controller
{
    public function index(): View
    {
        $user = Auth::user();

        // Ambil reservasi completed untuk dipilih saat nulis review
        $reservations = Reservation::with('court')
            ->where('user_id', $user->id)
            ->where('status_reservasi', 'completed')
            ->orderBy('tanggal_booking', 'desc')
            ->get();

        $reviewedCoachReservationIds = CoachReview::where('user_id', $user->id)
            ->whereNotNull('reservation_id')
            ->pluck('reservation_id');

        $coachReservations = Reservation::with(['court', 'coach.user'])
            ->where('user_id', $user->id)
            ->where('status_reservasi', 'completed')
            ->whereNotNull('coach_id')
            ->whereNotIn('id', $reviewedCoachReservationIds)
            ->orderBy('tanggal_booking', 'desc')
            ->get();

        // Review yang sudah ditulis user ini
        $myFeedbacks = Feedback::with(['reservation.court'])
            ->where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->get();

        $myCoachReviews = CoachReview::with(['coach.user', 'reservation.court'])
            ->where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->get();

        // Global avg rating dari semua feedback
        $avgRating = Feedback::avg('rating') ?? 0;
        $totalReviews = Feedback::count();

        return view('user.reviews.index', compact(
            'reservations', 'coachReservations', 'myFeedbacks', 'myCoachReviews',
            'avgRating', 'totalReviews'
        ));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'reservation_id' => ['required', 'exists:reservations,id'],
            'kebersihan' => ['required', 'integer', 'min:1', 'max:5'],
            'kondisi_lapangan' => ['required', 'integer', 'min:1', 'max:5'],
            'komunikasi_staff' => ['required', 'integer', 'min:1', 'max:5'],
            'fasilitas' => ['required', 'integer', 'min:1', 'max:5'],
            'komentar_review' => ['nullable', 'string', 'max:1000'],
        ]);

        // Hitung rata-rata 4 kategori sebagai `rating`
        $avgRating = round(
            ($validated['kebersihan'] + $validated['kondisi_lapangan'] +
             $validated['komunikasi_staff'] + $validated['fasilitas']) / 4
        );

        Feedback::updateOrCreate(
            [
                'user_id' => Auth::id(),
                'reservation_id' => $validated['reservation_id'],
            ],
            [
                'rating' => $avgRating,
                'komentar_review' => $validated['komentar_review'] ?? null,
            ]
        );

        return redirect()->route('reviews.index')->with('review_sent', true);
    }
}
