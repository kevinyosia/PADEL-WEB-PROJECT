<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\CourtController; // <-- TAMBAHKAN INI DI SINI

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// Semua rute di dalam group ini wajib login
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    
    // <-- TAMBAHKAN RUTE COURTS DI SINI -->
    Route::get('/courts', [CourtController::class, 'index'])->name('courts.index');
});

Route::get('/booking', [BookingController::class, 'index'])->name('booking.index');

require __DIR__.'/auth.php';