<?php

namespace App\Http\Controllers;

use App\Models\Reservation;
use App\Models\Equipment;
use App\Models\Transaction;
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
        $bookingCounts = [];
        $today = Carbon::now();

        for ($i = 6; $i >= 0; $i--) {
            $date = $today->copy()->subDays($i);
            $day = $date->format('D');

            $dayRevenue = Transaction::whereHas('reservation', function ($query) use ($date) {
                $query->whereDate('created_at', $date->toDateString());
            })
            ->where('status_pembayaran', 'lunas')
            ->sum('grand_total');

            $dayBookings = Reservation::whereHas('transaction', function ($query) use ($date) {
                $query->where('status_pembayaran', 'lunas')
                    ->whereDate('created_at', $date->toDateString());
            })->count();

            $revenueData[] = [
                'day' => $day,
                'revenue' => $dayRevenue,
            ];

            $bookingCounts[] = [
                'day' => $day,
                'count' => $dayBookings,
            ];
        }

        // Recent Activity - last 5 bookings yang sudah lunas
        $recentActivity = Transaction::whereHas('reservation', function ($query) {
            $query->with('user', 'court');
        })
        ->where('status_pembayaran', 'lunas')
        ->latest('created_at')
        ->take(5)
        ->get()
        ->map(function ($transaction) {
            return [
                'activity' => $transaction->reservation->court->nama_lapangan . ' Booking (' . $transaction->reservation->durasi_jam . 'h)',
                'category' => 'Booking',
                'time' => $transaction->created_at->diffForHumans(),
                'amount' => $transaction->grand_total,
            ];
        });

        // Court Occupancy - courts yang sedang booked hari ini
        $today_date = Carbon::now()->toDateString();
        $totalCourts = \App\Models\Court::count();
        $occupiedCourts = Reservation::whereDate('tanggal_booking', $today_date)
            ->where('status_reservasi', 'confirmed')
            ->distinct('court_id')
            ->count();

        $courtOccupancy = $totalCourts > 0 ? round(($occupiedCourts / $totalCourts) * 100) : 0;

        // Peak Hour Today - jam yang paling banyak booking
        $peakHour = Reservation::whereDate('tanggal_booking', $today_date)
            ->where('status_reservasi', 'confirmed')
            ->selectRaw('jam_mulai, COUNT(*) as count')
            ->groupBy('jam_mulai')
            ->orderByDesc('count')
            ->first();

        $peakHourDisplay = $peakHour ? $peakHour->jam_mulai . ':00' : 'N/A';

        return view('manager.dashboard', compact(
            'totalBookings',
            'ballSales',
            'racketRentals',
            'bookingTrend',
            'ballSalesTrend',
            'racketRentalsTrend',
            'revenueData',
            'bookingCounts',
            'recentActivity',
            'courtOccupancy',
            'peakHourDisplay'
        ));
    }
}
