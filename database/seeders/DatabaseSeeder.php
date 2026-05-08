<?php

namespace Database\Seeders;

use App\Models\Bus;
use App\Models\BusRoute;
use App\Models\City;
use App\Models\Schedule;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Admin user
        User::factory()->create([
            'name' => 'Admin HDEX',
            'email' => 'admin@hdextrans.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);

        // Passenger user
        User::factory()->create([
            'name' => 'John Doe',
            'email' => 'user@example.com',
            'password' => Hash::make('password'),
            'role' => 'user',
        ]);

        // Cities
        $jakarta = City::create(['name' => 'Jakarta']);
        $surabaya = City::create(['name' => 'Surabaya']);
        $semarang = City::create(['name' => 'Semarang']);

        // Buses
        $bus1 = Bus::create([
            'name' => 'HDEX Royal Class',
            'plate_number' => 'B 1234 HDX',
            'total_seats' => 30,
            'layout_type' => '2-2',
        ]);

        $bus2 = Bus::create([
            'name' => 'HDEX Sleeper',
            'plate_number' => 'B 5678 HDX',
            'total_seats' => 22,
            'layout_type' => 'sleeper',
        ]);

        // Routes
        $route1 = BusRoute::create([
            'origin_city_id' => $jakarta->id,
            'destination_city_id' => $surabaya->id,
            'distance' => 800,
            'base_price' => 350000,
        ]);

        // Schedules
        Schedule::create([
            'bus_route_id' => $route1->id,
            'bus_id' => $bus1->id,
            'departure_time' => now()->addDays(1)->setTime(19, 0, 0),
            'arrival_time' => now()->addDays(2)->setTime(6, 0, 0),
            'price' => 350000,
        ]);

        Schedule::create([
            'bus_route_id' => $route1->id,
            'bus_id' => $bus2->id,
            'departure_time' => now()->addDays(1)->setTime(20, 0, 0),
            'arrival_time' => now()->addDays(2)->setTime(7, 0, 0),
            'price' => 450000, // Sleeper is more expensive
        ]);
    }
}
