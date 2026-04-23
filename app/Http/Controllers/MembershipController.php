<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Models\PointHistory;
use Illuminate\View\View;

class MembershipController extends Controller
{
    public function index(): View
    {
        $user = Auth::user();
        $membership = $user->membership;
        $isMember = $membership !== null;

        $pointHistories = collect();
        if ($isMember) {
            $pointHistories = PointHistory::where('user_id', $user->id)
                ->orderBy('created_at', 'desc')
                ->take(20)
                ->get();
        }

        return view('user.membership.index', compact('isMember', 'membership', 'pointHistories'));
    }
}
