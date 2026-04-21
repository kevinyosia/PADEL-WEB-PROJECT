<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\CourtController;
use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\AdminInventoryController;
use App\Http\Controllers\AdminCoachController;
use App\Http\Controllers\AdminLoginController;
use App\Http\Controllers\ManagerLoginController;
use App\Http\Controllers\ManagerDashboardController;
use App\Http\Controllers\ManagerReviewsController;
use App\Http\Controllers\CoachesPageController;
use App\Http\Controllers\ProShopController;
use App\Http\Controllers\MembershipController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\SettingsController;

Route::get('/', function () {
    return view('welcome');
});

// ── Manager Login Routes (MUST be outside auth middleware) ──
Route::prefix('manager')->name('manager.')->group(function () {
    Route::get('/login', [ManagerLoginController::class, 'showLoginForm'])
        ->middleware('guest')
        ->name('login');
    
    Route::post('/login', [ManagerLoginController::class, 'login'])
        ->middleware('guest')
        ->name('login.submit');
    
    Route::post('/logout', [ManagerLoginController::class, 'logout'])
        ->middleware('auth')
        ->name('logout');
});

// ── Admin Login Routes (MUST be outside auth middleware) ──
Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/login', [AdminLoginController::class, 'showLoginForm'])
        ->middleware('guest')
        ->name('login');
    
    Route::post('/login', [AdminLoginController::class, 'login'])
        ->middleware('guest')
        ->name('login.submit');
    
    Route::post('/logout', [AdminLoginController::class, 'logout'])
        ->middleware('auth')
        ->name('logout');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// Semua rute di dalam group ini wajib login
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::post('/logout', [ProfileController::class, 'logout'])->name('user.logout');
    
    // User panel routes
    Route::get('/courts', [CourtController::class, 'index'])->name('courts.index');
    Route::get('/courts/availability', [CourtController::class, 'availability'])->name('courts.availability');
    Route::get('/booking', [BookingController::class, 'index'])->name('booking.index');
    Route::post('/booking', [BookingController::class, 'store'])->name('booking.store');
    Route::get('/coaches', [CoachesPageController::class, 'index'])->name('coaches.index');
    Route::get('/membership', [MembershipController::class, 'index'])->name('membership.index');
    Route::get('/reviews', [ReviewController::class, 'index'])->name('reviews.index');
    Route::post('/reviews', [ReviewController::class, 'store'])->name('reviews.store');
    Route::get('/proshop', [ProShopController::class, 'index'])->name('proshop.index');
    Route::get('/settings', [SettingsController::class, 'index'])->name('settings.index');
    Route::patch('/settings/password', [SettingsController::class, 'updatePassword'])->name('settings.password');
    
    // Admin pricing management routes
    Route::middleware('admin')->prefix('admin/pricing')->as('admin.pricing.')->group(function () {
        Route::get('/', [\App\Http\Controllers\AdminCourtPricingController::class, 'index'])->name('index');
        Route::get('/courts/{court}/edit', [\App\Http\Controllers\AdminCourtPricingController::class, 'edit'])->name('edit');
        Route::patch('/courts/{court}', [\App\Http\Controllers\AdminCourtPricingController::class, 'update'])->name('update');
    });

    // Manager dashboard routes
    Route::middleware('manager')->prefix('manager')->as('manager.')->group(function () {
        Route::get('/dashboard', [ManagerDashboardController::class, 'index'])->name('dashboard');
        Route::get('/reviews', [ManagerReviewsController::class, 'index'])->name('reviews');
    });

    // Admin dashboard routes
    Route::middleware('admin')->prefix('admin')->as('admin.')->group(function () {
        Route::get('/dashboard/courts', [AdminDashboardController::class, 'courtManagement'])->name('dashboard.courts');
        Route::patch('/courts/{court}/status', [AdminDashboardController::class, 'updateCourtStatus'])->name('courts.update-status');
        
        // Inventory & rentals routes
        Route::get('/inventory', [AdminInventoryController::class, 'index'])->name('inventory.index');
        Route::patch('/consumables/{consumable}/price', [AdminInventoryController::class, 'updateConsumablePrice'])->name('consumables.update-price');
        Route::patch('/rental-items/{rentalItem}/rate', [AdminInventoryController::class, 'updateRentalItemRate'])->name('rental-items.update-rate');
        
        // Coach management routes
        Route::get('/coaches', [AdminCoachController::class, 'index'])->name('coaches.index');
        Route::get('/coaches/create', [AdminCoachController::class, 'create'])->name('coaches.create');
        Route::post('/coaches', [AdminCoachController::class, 'store'])->name('coaches.store');
        Route::get('/coaches/{coach}/edit', [AdminCoachController::class, 'edit'])->name('coaches.edit');
        Route::patch('/coaches/{coach}', [AdminCoachController::class, 'update'])->name('coaches.update');
        Route::patch('/coaches/{coach}/availability', [AdminCoachController::class, 'updateAvailability'])->name('coaches.update-availability');
        Route::delete('/coaches/{coach}', [AdminCoachController::class, 'destroy'])->name('coaches.destroy');
    });
});

require __DIR__.'/auth.php';