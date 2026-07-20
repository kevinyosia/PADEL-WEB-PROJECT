<?php

use App\Http\Controllers\AdminCoachController;
use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\AdminInventoryController;
use App\Http\Controllers\AdminLoginController;
use App\Http\Controllers\AdminUserController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\CoachesPageController;
use App\Http\Controllers\CoachReviewController;
use App\Http\Controllers\CourtController;
use App\Http\Controllers\ManagerDashboardController;
use App\Http\Controllers\ManagerLoginController;
use App\Http\Controllers\ManagerReviewsController;
use App\Http\Controllers\MembershipController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProShopController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\SettingsController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// ── Debug route ──
Route::get('/debug/transaction/{id}', function ($id) {
    $transaction = \App\Models\Transaction::find($id);
    if (! $transaction) {
        return response()->json(['error' => 'Transaction not found'], 404);
    }

    return response()->json([
        'id' => $transaction->id,
        'status_pembayaran' => $transaction->status_pembayaran,
        'grand_total' => $transaction->grand_total,
        'snap_token' => $transaction->snap_token,
        'midtrans_order_id' => $transaction->midtrans_order_id,
        'reservation_id' => $transaction->reservation_id,
    ]);
});

// Reset all transactions to belum_lunas for testing
Route::get('/debug/reset-transactions', function () {
    \App\Models\Transaction::query()->update(['status_pembayaran' => 'belum_lunas']);

    return response()->json(['message' => 'All transactions reset to belum_lunas']);
});

// ── Midtrans Webhook (outside auth middleware) ──
Route::post('/webhook/midtrans', [PaymentController::class, 'webhook'])
    ->withoutMiddleware(\Illuminate\Foundation\Http\Middleware\ValidateCsrfToken::class);

// ── Manager Login Routes (MUST be outside auth middleware) ──
Route::prefix('manajemen')->name('manager.')->group(function () {
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
    Route::post('/profile/photo', [ProfileController::class, 'updatePhoto'])->name('profile.update-photo');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::post('/logout', [ProfileController::class, 'logout'])->name('user.logout');

    // User panel routes
    Route::get('/courts', [CourtController::class, 'index'])->name('courts.index');
    Route::get('/courts/availability', [CourtController::class, 'availability'])->name('courts.availability');
    Route::get('/booking', [BookingController::class, 'index'])->name('booking.index');
    Route::post('/booking', [BookingController::class, 'store'])->name('booking.store');

    // Payment routes
    Route::post('/payment/snap-token', [PaymentController::class, 'generateSnapToken'])->name('payment.snap-token');
    Route::get('/payment/{transaction}', [PaymentController::class, 'paymentPage'])->name('payment.page');
    Route::get('/payment/{transaction}/status', [PaymentController::class, 'checkStatus'])->name('payment.status');
    Route::get('/payment/{transaction}/complete', [PaymentController::class, 'complete'])->name('payment.complete');
    Route::post('/payment/{transaction}/abandon', [PaymentController::class, 'abandon'])->name('payment.abandon');

    Route::get('/coaches', [CoachesPageController::class, 'index'])->name('coaches.index');
    Route::get('/coaches/{coach}/slots', [CoachesPageController::class, 'slots'])->name('coaches.slots');

    // Coach review routes (user can submit & view)
    Route::get('/coach/{coach}/reviews', [CoachReviewController::class, 'getCoachReviews'])->name('coach.reviews.get');
    Route::post('/coach/{coach}/reviews', [CoachReviewController::class, 'store'])->name('coach.reviews.store');
    Route::delete('/reviews/{review}', [CoachReviewController::class, 'destroy'])->name('coach.reviews.destroy');

    Route::get('/membership', [MembershipController::class, 'index'])->name('membership.index');
    Route::post('/membership/snap-token', [MembershipController::class, 'generateSnapToken'])->name('membership.snap-token');
    Route::get('/membership/complete', [MembershipController::class, 'complete'])->name('membership.complete');
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
    Route::middleware('manager')->prefix('manajemen')->as('manager.')->group(function () {
        Route::get('/dashboard', [ManagerDashboardController::class, 'index'])->name('dashboard');
        Route::get('/reviews', [ManagerReviewsController::class, 'index'])->name('reviews');

        // Coach reviews management
        Route::get('/coach/{coach}/reviews', [CoachReviewController::class, 'getCoachReviews'])->name('coach.reviews');
    });

    // Admin dashboard routes
    Route::middleware('admin')->prefix('admin')->as('admin.')->group(function () {
        Route::get('/dashboard/courts', [AdminDashboardController::class, 'courtManagement'])->name('dashboard.courts');
        Route::patch('/courts/{court}/status', [AdminDashboardController::class, 'updateCourtStatus'])->name('courts.update-status');

        // Inventory & rentals routes
        Route::get('/inventory', [AdminInventoryController::class, 'index'])->name('inventory.index');
        // Equipment price update (for both sales and rental)
        Route::patch('/equipment/{equipment}/price', [AdminInventoryController::class, 'updateEquipmentPrice'])->name('equipment.update-price');
        Route::patch('/equipment/{equipment}/rate', [AdminInventoryController::class, 'updateEquipmentRate'])->name('equipment.update-rate');
        Route::patch('/equipment/{equipment}/stock', [AdminInventoryController::class, 'updateEquipmentStock'])->name('equipment.update-stock');
        Route::post('/equipment', [AdminInventoryController::class, 'storeEquipment'])->name('equipment.store');
        // Legacy routes for backward compatibility
        Route::patch('/consumables/{consumable}/price', [AdminInventoryController::class, 'updateConsumablePrice'])->name('consumables.update-price');
        Route::patch('/consumables/{consumable}/stock', [AdminInventoryController::class, 'updateConsumableStock'])->name('consumables.update-stock');
        Route::post('/consumables', [AdminInventoryController::class, 'storeConsumable'])->name('consumables.store');
        Route::patch('/rental-items/{equipment}/rate', [AdminInventoryController::class, 'updateRentalItemRate'])->name('rental-items.update-rate');

        // Coach management routes
        Route::get('/coaches', [AdminCoachController::class, 'index'])->name('coaches.index');
        Route::get('/coaches/create', [AdminCoachController::class, 'create'])->name('coaches.create');
        Route::post('/coaches', [AdminCoachController::class, 'store'])->name('coaches.store');
        Route::get('/coaches/{coach}/edit', [AdminCoachController::class, 'edit'])->name('coaches.edit');
        Route::patch('/coaches/{coach}', [AdminCoachController::class, 'update'])->name('coaches.update');
        Route::patch('/coaches/{coach}/availability', [AdminCoachController::class, 'updateAvailability'])->name('coaches.update-availability');
        Route::delete('/coaches/{coach}', [AdminCoachController::class, 'destroy'])->name('coaches.destroy');

        // User management routes
        Route::get('/users', [AdminUserController::class, 'index'])->name('users.index');
        Route::get('/users/{user}', [AdminUserController::class, 'show'])->name('users.show');
        Route::patch('/users/{user}/ban', [AdminUserController::class, 'ban'])->name('users.ban');
        Route::patch('/users/{user}/unban', [AdminUserController::class, 'unban'])->name('users.unban');
        Route::delete('/users/{user}', [AdminUserController::class, 'anonymize'])->name('users.anonymize');
    });
});

require __DIR__.'/auth.php';
