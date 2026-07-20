<?php

namespace App\Http\Controllers;

use App\Models\Reservation;
use App\Models\Equipment;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Carbon\Carbon;

class ManagerDashboardController extends Controller
{
    /**
     * Tampilkan dashboard manajemen dengan metrics
     */
    public function index(): View
    {
        // Total Bookings - dari reservations dengan transaksi yang berhasil (lunas)
        $totalBookings = Reservation::whereHas('transaction', function ($query) {
            $query->where('status_pembayaran', 'lunas');
        })->count();

        // Ball Sales - dari equipment kategori 'beli' yang terjual
        $ballSales = Transaction::whereHas('reservation', function ($query) {
            $query->whereHas('equipment', function ($equipmentQuery) {
                $equipmentQuery->where('kategori', 'beli');
            });
        })
        ->where('status_pembayaran', 'lunas')
        ->sum('total_harga_perlengkapan');

        // Racket Rentals - count dari equipment kategori 'sewa'
        $racketRentals = Reservation::whereHas('equipment', function ($query) {
            $query->where('kategori', 'sewa');
        })
        ->whereHas('transaction', function ($query) {
            $query->where('status_pembayaran', 'lunas');
        })->count();

        // Calculate trends (last 30 days vs previous 30 days)
        $thirtyDaysAgo = Carbon::now()->subDays(30);
        $sixtyDaysAgo = Carbon::now()->subDays(60);

        $currentBookings = Reservation::whereHas('transaction', function ($query) {
            $query->where('status_pembayaran', 'lunas');
        })
        ->where('created_at', '>=', $thirtyDaysAgo)
        ->count();

        $previousBookings = Reservation::whereHas('transaction', function ($query) {
            $query->where('status_pembayaran', 'lunas');
        })
        ->whereBetween('created_at', [$sixtyDaysAgo, $thirtyDaysAgo])
        ->count();

        $bookingTrend = $previousBookings > 0
            ? round(((($currentBookings - $previousBookings) / $previousBookings) * 100), 2)
            : 0;

        // Ball Sales Trend
        $currentBallSales = Transaction::whereHas('reservation', function ($query) {
            $query->whereHas('equipment', function ($equipmentQuery) {
                $equipmentQuery->where('kategori', 'beli');
            });
        })
        ->where('status_pembayaran', 'lunas')
        ->where('created_at', '>=', $thirtyDaysAgo)
        ->sum('total_harga_perlengkapan');

        $previousBallSales = Transaction::whereHas('reservation', function ($query) {
            $query->whereHas('equipment', function ($equipmentQuery) {
                $equipmentQuery->where('kategori', 'beli');
            });
        })
        ->where('status_pembayaran', 'lunas')
        ->whereBetween('created_at', [$sixtyDaysAgo, $thirtyDaysAgo])
        ->sum('total_harga_perlengkapan');

        $ballSalesTrend = $previousBallSales > 0
            ? round(((($currentBallSales - $previousBallSales) / $previousBallSales) * 100), 2)
            : 0;

        // Racket Rentals Trend
        $currentRacketRentals = Reservation::whereHas('equipment', function ($query) {
            $query->where('kategori', 'sewa');
        })
        ->whereHas('transaction', function ($query) {
            $query->where('status_pembayaran', 'lunas');
        })
        ->where('created_at', '>=', $thirtyDaysAgo)
        ->count();

        $previousRacketRentals = Reservation::whereHas('equipment', function ($query) {
            $query->where('kategori', 'sewa');
        })
        ->whereHas('transaction', function ($query) {
            $query->where('status_pembayaran', 'lunas');
        })
        ->whereBetween('created_at', [$sixtyDaysAgo, $thirtyDaysAgo])
        ->count();

        $racketRentalsTrend = $previousRacketRentals > 0
            ? round(((($currentRacketRentals - $previousRacketRentals) / $previousRacketRentals) * 100), 2)
            : 0;

        // Revenue & Usage Trends - last 7 days
        $revenueData = [];
        $today = Carbon::now();

        for ($i = 6; $i >= 0; $i--) {
            $date = $today->copy()->subDays($i);
            $day = $date->format('D');

            $daySales = Transaction::whereHas('reservation', function ($query) use ($date) {
                $query->whereDate('created_at', $date->toDateString())
                    ->whereHas('equipment', function ($equipmentQuery) {
                        $equipmentQuery->where('kategori', 'beli');
                    });
            })
            ->where('status_pembayaran', 'lunas')
            ->sum('total_harga_perlengkapan');

            $dayBookings = Reservation::whereHas('transaction', function ($query) use ($date) {
                $query->where('status_pembayaran', 'lunas')
                    ->whereDate('created_at', $date->toDateString());
            })->count();

            $revenueData[] = [
                'day' => $day,
                'bookings' => $dayBookings,
                'sales' => $daySales,
            ];
        }

        $chartMaxBookings = max(1, max(array_column($revenueData, 'bookings')));
        $chartMaxSales = max(1, max(array_column($revenueData, 'sales')));

        // Recent Activity - last 5 bookings yang sudah lunas
        $recentActivity = Transaction::with(['reservation' => function ($query) {
            $query->with(['user', 'court']);
        }])
        ->where('status_pembayaran', 'lunas')
        ->latest('created_at')
        ->take(5)
        ->get()
        ->map(function ($transaction) {
            // Hitung durasi dari jam_mulai dan jam_selesai
            $mulai = \Carbon\Carbon::createFromFormat('H:i:s', $transaction->reservation->jam_mulai ?? '00:00:00');
            $selesai = \Carbon\Carbon::createFromFormat('H:i:s', $transaction->reservation->jam_selesai ?? '01:00:00');
            if ($selesai <= $mulai) {
                $selesai->addDay();
            }
            $durasiJam = max(1, $selesai->diffInHours($mulai));
            
            return [
                'activity' => $transaction->reservation->court->nama_lapangan . ' Booking (' . $durasiJam . 'h)',
                'category' => 'Booking',
                'time' => $transaction->created_at->diffForHumans(),
                'amount' => $transaction->grand_total,
            ];
        });

        // New Members - Users yang sign up kemarin (dari role 'customer')
        $yesterday = Carbon::now()->subDay()->startOfDay();
        $newMembers = User::where('role', 'customer')
            ->where('created_at', '>=', $yesterday)
            ->count();

        return view('manager.dashboard', compact(
            'totalBookings',
            'ballSales',
            'racketRentals',
            'bookingTrend',
            'ballSalesTrend',
            'racketRentalsTrend',
            'revenueData',
            'chartMaxBookings',
            'chartMaxSales',
            'recentActivity',
            'newMembers'
        ));
    }
}
