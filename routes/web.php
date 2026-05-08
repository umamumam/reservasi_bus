<?php

use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Route;
use Laravel\Fortify\Features;

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::post('/api/search', [HomeController::class, 'search'])->name('api.search');
Route::get('/schedule/{schedule}/seats', [HomeController::class, 'seats'])->name('schedule.seats');
Route::post('/booking', [HomeController::class, 'book'])->name('booking.store')->middleware(['auth', 'verified']);
Route::get('/booking/{booking}/payment', [HomeController::class, 'payment'])->name('booking.payment')->middleware(['auth', 'verified']);
Route::post('/booking/{booking}/pay', [HomeController::class, 'pay'])->name('booking.pay')->middleware(['auth', 'verified']);
Route::get('/booking/{booking}/ticket', [HomeController::class, 'ticket'])->name('booking.ticket')->middleware(['auth', 'verified']);
Route::get('/booking/{booking}/pdf', [App\Http\Controllers\TicketController::class, 'downloadPdf'])->name('booking.pdf')->middleware(['auth', 'verified']);

use App\Http\Controllers\DashboardController;
use App\Models\Bus;
use App\Models\BusRoute;
use App\Models\Schedule;
use App\Models\Booking;
use Inertia\Inertia;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', function (Illuminate\Http\Request $request) {
        if ($request->user()->role === 'admin') {
            return app(App\Http\Controllers\DashboardController::class)->index($request);
        }
        return redirect('/');
    })->name('dashboard');

    // User Routes
    Route::prefix('user')->group(function () {
        Route::get('/bookings', function (Illuminate\Http\Request $request) {
            return Inertia::render('User/Bookings/Index', [
                'bookings' => Booking::where('user_id', $request->user()->id)->with('schedule.busRoute.originCity', 'schedule.busRoute.destinationCity', 'schedule.bus')->get()
            ]);
        })->name('user.bookings');
    });

        // Admin Routes
    Route::prefix('admin')->group(function () {
        
        Route::get('/cities', function (Illuminate\Http\Request $request) {
            if ($request->user()->role !== 'admin') abort(403);
            return Inertia::render('Admin/Cities/Index', ['cities' => \App\Models\City::latest()->get()]);
        })->name('admin.cities');

        Route::post('/cities', function (Illuminate\Http\Request $request) {
            if ($request->user()->role !== 'admin') abort(403);
            \App\Models\City::create($request->validate([
                'name' => 'required|string|max:255|unique:cities',
            ]));
            return back();
        })->name('admin.cities.store');

        Route::put('/cities/{city}', function (Illuminate\Http\Request $request, \App\Models\City $city) {
            if ($request->user()->role !== 'admin') abort(403);
            $city->update($request->validate([
                'name' => 'required|string|max:255|unique:cities,name,' . $city->id,
            ]));
            return back();
        })->name('admin.cities.update');

        Route::delete('/cities/{city}', function (Illuminate\Http\Request $request, \App\Models\City $city) {
            if ($request->user()->role !== 'admin') abort(403);
            $city->delete();
            return back();
        })->name('admin.cities.destroy');

        Route::get('/buses', function (Illuminate\Http\Request $request) {
            if ($request->user()->role !== 'admin') abort(403);
            return Inertia::render('Admin/Buses/Index', ['buses' => Bus::latest()->get()]);
        })->name('admin.buses');

        Route::post('/buses', function (Illuminate\Http\Request $request) {
            if ($request->user()->role !== 'admin') abort(403);
            Bus::create($request->validate([
                'name' => 'required|string|max:255',
                'plate_number' => 'required|string|max:255|unique:buses',
                'layout_type' => 'required|string|in:2-2,sleeper',
                'total_seats' => 'required|integer|min:1',
            ]));
            return back();
        })->name('admin.buses.store');

        Route::put('/buses/{bus}', function (Illuminate\Http\Request $request, Bus $bus) {
            if ($request->user()->role !== 'admin') abort(403);
            $bus->update($request->validate([
                'name' => 'required|string|max:255',
                'plate_number' => 'required|string|max:255|unique:buses,plate_number,' . $bus->id,
                'layout_type' => 'required|string|in:2-2,sleeper',
                'total_seats' => 'required|integer|min:1',
            ]));
            return back();
        })->name('admin.buses.update');

        Route::delete('/buses/{bus}', function (Illuminate\Http\Request $request, Bus $bus) {
            if ($request->user()->role !== 'admin') abort(403);
            $bus->delete();
            return back();
        })->name('admin.buses.destroy');

        Route::get('/routes', function (Illuminate\Http\Request $request) {
            if ($request->user()->role !== 'admin') abort(403);
            return Inertia::render('Admin/Routes/Index', [
                'routes' => BusRoute::with('originCity', 'destinationCity')->latest()->get(),
                'cities' => \App\Models\City::all()
            ]);
        })->name('admin.routes');

        Route::post('/routes', function (Illuminate\Http\Request $request) {
            if ($request->user()->role !== 'admin') abort(403);
            BusRoute::create($request->validate([
                'origin_city_id' => 'required|exists:cities,id',
                'destination_city_id' => 'required|exists:cities,id|different:origin_city_id',
                'distance' => 'required|numeric|min:1',
                'base_price' => 'required|numeric|min:0',
            ]));
            return back();
        })->name('admin.routes.store');

        Route::put('/routes/{route}', function (Illuminate\Http\Request $request, BusRoute $route) {
            if ($request->user()->role !== 'admin') abort(403);
            $route->update($request->validate([
                'origin_city_id' => 'required|exists:cities,id',
                'destination_city_id' => 'required|exists:cities,id|different:origin_city_id',
                'distance' => 'required|numeric|min:1',
                'base_price' => 'required|numeric|min:0',
            ]));
            return back();
        })->name('admin.routes.update');

        Route::delete('/routes/{route}', function (Illuminate\Http\Request $request, BusRoute $route) {
            if ($request->user()->role !== 'admin') abort(403);
            $route->delete();
            return back();
        })->name('admin.routes.destroy');

        Route::get('/schedules', function (Illuminate\Http\Request $request) {
            if ($request->user()->role !== 'admin') abort(403);
            return Inertia::render('Admin/Schedules/Index', [
                'schedules' => Schedule::with('bus', 'busRoute.originCity', 'busRoute.destinationCity')->latest()->get(),
                'buses' => Bus::all(),
                'routes' => BusRoute::with('originCity', 'destinationCity')->get()
            ]);
        })->name('admin.schedules');

        Route::post('/schedules', function (Illuminate\Http\Request $request) {
            if ($request->user()->role !== 'admin') abort(403);
            Schedule::create($request->validate([
                'bus_id' => 'required|exists:buses,id',
                'bus_route_id' => 'required|exists:bus_routes,id',
                'departure_time' => 'required|date',
                'arrival_time' => 'required|date|after:departure_time',
                'price' => 'required|numeric|min:0',
            ]));
            return back();
        })->name('admin.schedules.store');

        Route::put('/schedules/{schedule}', function (Illuminate\Http\Request $request, Schedule $schedule) {
            if ($request->user()->role !== 'admin') abort(403);
            $schedule->update($request->validate([
                'bus_id' => 'required|exists:buses,id',
                'bus_route_id' => 'required|exists:bus_routes,id',
                'departure_time' => 'required|date',
                'arrival_time' => 'required|date|after:departure_time',
                'price' => 'required|numeric|min:0',
            ]));
            return back();
        })->name('admin.schedules.update');

        Route::delete('/schedules/{schedule}', function (Illuminate\Http\Request $request, Schedule $schedule) {
            if ($request->user()->role !== 'admin') abort(403);
            $schedule->delete();
            return back();
        })->name('admin.schedules.destroy');

        Route::get('/bookings', function (Illuminate\Http\Request $request) {
            if ($request->user()->role !== 'admin') abort(403);
            return Inertia::render('Admin/Bookings/Index', ['bookings' => Booking::with('user', 'schedule.bus', 'schedule.busRoute.originCity', 'schedule.busRoute.destinationCity')->latest()->get()]);
        })->name('admin.bookings');

        Route::get('/users', function (Illuminate\Http\Request $request) {
            if ($request->user()->role !== 'admin') abort(403);
            return Inertia::render('Admin/Users/Index', [
                'users' => \App\Models\User::latest()->get()
            ]);
        })->name('admin.users');

        Route::delete('/users/{user}', function (Illuminate\Http\Request $request, \App\Models\User $user) {
            if ($request->user()->role !== 'admin') abort(403);
            if ($user->role === 'admin') abort(403, 'Cannot delete admin');
            $user->delete();
            return back();
        })->name('admin.users.destroy');
    });
});

require __DIR__.'/settings.php';
