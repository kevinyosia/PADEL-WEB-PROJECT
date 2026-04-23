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
            $query->where(function ($q) use ($searchQuery) {
                $q->whereHas('user', function ($subQuery) use ($searchQuery) {
                    $subQuery->where('name', 'like', '%' . $searchQuery . '%');
                })
                ->orWhere('komentar_review', 'like', '%' . $searchQuery . '%');
            });
        }

        // Paginate reviews
        $reviews = $query->latest('created_at')->paginate(10);

        // Arena Sentiment - rata-rata rating per kategori feedback
        // Gunakan query aggregation instead of loading all records untuk performa lebih baik
        $allFeedbacksCount = Feedback::count();
        $arenaRating = round(Feedback::avg('rating') ?? 0, 1);

        // Untuk detail breakdown, kita hitung dari rating tunggal yang tersimpan
        $courtCondition = round(Feedback::avg('rating') ?? 0, 1);
        $staffCommunication = round(Feedback::avg('rating') ?? 0, 1);
        $facilityCleanless = round(Feedback::avg('rating') ?? 0, 1);
        $overallExperience = round(Feedback::avg('rating') ?? 0, 1);

        // Review Analytics
        $totalReviewsThisMonth = Feedback::where('created_at', '>=', now()->startOfMonth())->count();
        $positiveSentimentPercent = $allFeedbacksCount > 0 
            ? round((Feedback::where('rating', '>=', 4)->count() / $allFeedbacksCount) * 100)
            : 0;

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
