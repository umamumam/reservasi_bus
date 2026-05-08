<?php

namespace App\Http\Controllers;

use App\Models\City;
use App\Models\Schedule;
use Illuminate\Http\Request;
use Inertia\Inertia;

class HomeController extends Controller
{
    public function index()
    {
        $cities = City::orderBy('name')->get();
        $busRoutes = \App\Models\BusRoute::select('origin_city_id', 'destination_city_id')->get();

        return Inertia::render('Welcome', [
            'cities' => $cities,
            'busRoutes' => $busRoutes,
        ]);
    }

    public function search(Request $request)
    {
        $request->validate([
            'origin' => 'required|exists:cities,id',
            'destination' => 'required|exists:cities,id|different:origin',
            'date' => 'required|date',
        ]);

        $schedules = Schedule::with(['bus', 'busRoute.originCity', 'busRoute.destinationCity'])
            ->with(['bookings' => function($query) {
                $query->where('status', '!=', 'cancelled')->withCount('seats');
            }])
            ->whereHas('busRoute', function ($query) use ($request) {
                $query->where('origin_city_id', $request->origin)
                      ->where('destination_city_id', $request->destination);
            })
            ->whereDate('departure_time', $request->date)
            ->get()
            ->map(function ($schedule) {
                $bookedCount = $schedule->bookings->sum('seats_count');
                $schedule->available_seats = $schedule->bus->total_seats - $bookedCount;
                return $schedule;
            });

        return response()->json($schedules);
    }

    public function seats(Schedule $schedule)
    {
        $schedule->load(['bus', 'busRoute.originCity', 'busRoute.destinationCity', 'bookings.seats']);

        // Extract booked seats
        $bookedSeats = $schedule->bookings()->where('status', '!=', 'cancelled')->with('seats')->get()
            ->pluck('seats')->flatten()->pluck('seat_number')->toArray();

        return Inertia::render('SeatSelection', [
            'schedule' => $schedule,
            'bookedSeats' => $bookedSeats,
        ]);
    }

    public function book(Request $request)
    {
        $request->validate([
            'schedule_id' => 'required|exists:schedules,id',
            'seats' => 'required|array|min:1',
            'seats.*' => 'required|string',
        ]);

        $schedule = Schedule::findOrFail($request->schedule_id);
        
        // Check availability again
        $bookedSeats = $schedule->bookings()->where('status', '!=', 'cancelled')->with('seats')->get()
            ->pluck('seats')->flatten()->pluck('seat_number')->toArray();
            
        foreach ($request->seats as $seat) {
            if (in_array($seat, $bookedSeats)) {
                return back()->withErrors(['seats' => "Kursi $seat sudah tidak tersedia."]);
            }
        }

        $booking = \App\Models\Booking::create([
            'user_id' => $request->user()->id,
            'schedule_id' => $request->schedule_id,
            'total_price' => count($request->seats) * $schedule->price,
            'status' => 'pending',
            'booking_code' => 'HDX' . strtoupper(substr(md5(uniqid()), 0, 8)),
        ]);

        foreach ($request->seats as $seat) {
            $booking->seats()->create([
                'seat_number' => $seat,
                'passenger_name' => $request->user()->name, // Default to user name
            ]);
        }

        return redirect()->route('booking.payment', $booking->id);
    }

    public function payment(\App\Models\Booking $booking)
    {
        if ($booking->user_id !== auth()->id()) abort(403);
        
        $booking->load(['schedule.bus', 'schedule.busRoute.originCity', 'schedule.busRoute.destinationCity', 'seats']);
        
        return \Inertia\Inertia::render('Payment', [
            'booking' => $booking
        ]);
    }

    public function pay(\App\Models\Booking $booking)
    {
        if ($booking->user_id !== auth()->id()) abort(403);
        
        $booking->update(['status' => 'paid']);
        
        return redirect()->route('user.bookings');
    }

    public function ticket(\App\Models\Booking $booking)
    {
        if ($booking->user_id !== auth()->id() && auth()->user()->role !== 'admin') abort(403);
        
        if ($booking->status !== 'paid') {
            return redirect()->route('booking.payment', $booking->id);
        }

        $booking->load(['schedule.bus', 'schedule.busRoute.originCity', 'schedule.busRoute.destinationCity', 'seats', 'user']);
        
        return \Inertia\Inertia::render('Ticket', [
            'booking' => $booking
        ]);
    }
}
