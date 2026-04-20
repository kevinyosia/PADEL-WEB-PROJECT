<?php

namespace App\Http\Controllers;

use App\Models\Coach;
use Illuminate\View\View;

class CoachesPageController extends Controller
{
    public function index(): View
    {
        $coaches = Coach::with(['user', 'reviews'])
            ->where('availability_status', '<>', 'deleted')
            ->get();
        
        return view('user.coaches.index', compact('coaches'));
    }
}
