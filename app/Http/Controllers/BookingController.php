<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\Court;
use App\Models\Coach;
use App\Models\Equipment;

class BookingController extends Controller
{
    public function index()
    {
        
        $timeSlots = [];
        $start = Carbon::createFromTime(6, 0); 
        $end = Carbon::createFromTime(0, 0)->addDay();

        while ($start < $end) {
            $timeSlots[] = $start->format('H:i');
            $start->addHour();
        }

        
        if (!in_array('00:00', $timeSlots) && !in_array('24:00', $timeSlots)) {
            $timeSlots[] = '00:00';
        }

        
        $courts = Court::where('status', 'tersedia')->get();
        
        
        $coaches = Coach::all();
        
       
        $equipments = Equipment::all();

        
        return view('booking.index', compact('timeSlots', 'courts', 'coaches', 'equipments'));
    }
}