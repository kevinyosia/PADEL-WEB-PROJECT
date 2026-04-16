<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CourtController extends Controller
{
    public function index()
    {
        // Akan mengarahkan ke file views/courts/index.blade.php
        return view('courts.index');
    }
}