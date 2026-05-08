<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Bus;
use App\Models\BusRoute;
use App\Models\Schedule;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        if ($user->role === 'admin') {
            $stats = [
                'total_buses' => Bus::count(),
                'total_routes' => BusRoute::count(),
                'total_schedules' => Schedule::count(),
                'total_bookings' => Booking::count(),
                'revenue' => Booking::where('status', 'paid')->sum('total_price'),
            ];

            $recentBookings = Booking::with(['user', 'schedule.busRoute.originCity', 'schedule.busRoute.destinationCity'])
                ->orderBy('created_at', 'desc')
                ->take(5)
                ->get();

            // Data for Revenue Chart (Last 7 Days)
            $dailyRevenue = [];
            for ($i = 6; $i >= 0; $i--) {
                $date = now()->subDays($i)->format('Y-m-d');
                $amount = Booking::where('status', 'paid')
                    ->whereDate('created_at', $date)
                    ->sum('total_price');
                $dailyRevenue[] = [
                    'date' => now()->subDays($i)->translatedFormat('d M'),
                    'amount' => $amount
                ];
            }

            return Inertia::render('Dashboard', [
                'stats' => $stats,
                'recentBookings' => $recentBookings,
                'chartData' => $dailyRevenue,
            ]);
        } else {
            // Passenger
            $bookings = Booking::where('user_id', $user->id)
                ->with(['schedule.bus', 'schedule.busRoute.originCity', 'schedule.busRoute.destinationCity', 'seats'])
                ->orderBy('created_at', 'desc')
                ->take(5)
                ->get();

            return Inertia::render('Dashboard', [
                'recentBookings' => $bookings,
            ]);
        }
    }
}
