<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\CourtController;
use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\AdminInventoryController;

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
    Route::get('/courts/availability', [CourtController::class, 'availability'])->name('courts.availability');
    Route::post('/booking', [BookingController::class, 'store'])->name('booking.store');
    
    // Admin pricing management routes
    Route::middleware('admin')->prefix('admin/pricing')->as('admin.pricing.')->group(function () {
        Route::get('/', [\App\Http\Controllers\AdminCourtPricingController::class, 'index'])->name('index');
        Route::get('/courts/{court}/edit', [\App\Http\Controllers\AdminCourtPricingController::class, 'edit'])->name('edit');
        Route::patch('/courts/{court}', [\App\Http\Controllers\AdminCourtPricingController::class, 'update'])->name('update');
    });

    // Admin dashboard routes
    Route::middleware('admin')->prefix('admin')->as('admin.')->group(function () {
        Route::get('/dashboard/courts', [AdminDashboardController::class, 'courtManagement'])->name('dashboard.courts');
        Route::patch('/courts/{court}/status', [AdminDashboardController::class, 'updateCourtStatus'])->name('courts.update-status');
        
        // Inventory & rentals routes
        Route::get('/inventory', [AdminInventoryController::class, 'index'])->name('inventory.index');
        Route::patch('/consumables/{consumable}/price', [AdminInventoryController::class, 'updateConsumablePrice'])->name('consumables.update-price');
        Route::patch('/rental-items/{rentalItem}/rate', [AdminInventoryController::class, 'updateRentalItemRate'])->name('rental-items.update-rate');
    });
});

Route::get('/booking', [BookingController::class, 'index'])->name('booking.index');

require __DIR__.'/auth.php';