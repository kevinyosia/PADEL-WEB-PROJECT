<?php

namespace App\Http\Controllers;

use App\Models\Feedback;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ManagerReviewsController extends Controller
{
    /**
     * Tampilkan halaman reviews management
     */
    public function index(Request $request): View
    {
        $ratingFilter = $request->get('rating');
        $searchQuery = $request->get('search');

        // Base query dengan relationships
        $query = Feedback::with('user', 'reservation');

        // Filter by rating jika ada
        if ($ratingFilter && in_array($ratingFilter, ['1', '2', '3', '4', '5'])) {
            $query->where('rating', (int)$ratingFilter);
        }

        // Search by user name atau review comment
        if ($searchQuery) {
            $query->whereHas('user', function ($q) use ($searchQuery) {
                $q->where('name', 'like', '%' . $searchQuery . '%');
            })->orWhere('komentar_review', 'like', '%' . $searchQuery . '%');
        }

        // Paginate reviews
        $reviews = $query->latest('created_at')->paginate(10);

        // Arena Sentiment - rata-rata rating per kategori feedback
        // Berdasarkan feedback table dengan rating tunggal (bukan 4 star inputs)
        $allFeedbacks = Feedback::all();
        
        // Calculate average for each category
        // Untuk sekarang, kita hitung dari rating tunggal yang tersimpan
        $arenaRating = round($allFeedbacks->avg('rating'), 1);

        // Untuk detail breakdown, kita bisa gunakan CoachReview untuk mendapat detail
        // Tapi Feedback hanya punya single rating field
        // Jadi kita calculate dari semua feedback
        $courtCondition = round($allFeedbacks->avg('rating'), 1);
        $staffCommunication = round($allFeedbacks->avg('rating'), 1);
        $facilityCleanless = round($allFeedbacks->avg('rating'), 1);
        $overallExperience = round($allFeedbacks->avg('rating'), 1);

        // Review Analytics
        $totalReviewsThisMonth = Feedback::where('created_at', '>=', now()->startOfMonth())->count();
        $positiveSentimentPercent = round(
            (Feedback::where('rating', '>=', 4)->count() / max($allFeedbacks->count(), 1)) * 100
        );

        return view('manager.reviews', compact(
            'reviews',
            'ratingFilter',
            'searchQuery',
            'arenaRating',
            'courtCondition',
            'staffCommunication',
            'facilityCleanless',
            'overallExperience',
            'totalReviewsThisMonth',
            'positiveSentimentPercent'
        ));
    }
}
