<?php

namespace App\Controllers;

use App\Models\Port;
use App\Models\Ship;
use App\Models\Trip;

class Home extends BaseController
{
    public function index()
    {
        $now = date('Y-m-d H:i:s');
        $capacity = (new Ship())->selectSum('capacity')->first();

        return view('home/index', [
            'isLoggedIn' => (bool) session()->get('isLoggedIn'),
            'publicMetrics' => [
                'ports' => (new Port())->countAllResults(),
                'ships' => (new Ship())->countAllResults(),
                'trips' => (new Trip())->countAllResults(),
                'activeTrips' => (new Trip())->where('departure_date <=', $now)->where('arrival_date >=', $now)->countAllResults(),
                'capacity' => (int) ($capacity['capacity'] ?? 0),
            ],
            'latestTrips' => (new Trip())
                ->select('trips.id, trips.departure_date, ships.name AS ship_name, departure_ports.name AS departure_port_name, arrival_ports.name AS arrival_port_name')
                ->join('ships', 'ships.id = trips.ship_id')
                ->join('ports AS departure_ports', 'departure_ports.id = trips.departure_port_id')
                ->join('ports AS arrival_ports', 'arrival_ports.id = trips.arrival_port_id')
                ->where('trips.departure_date >=', $now)
                ->orderBy('trips.departure_date', 'ASC')
                ->findAll(4),
        ]);
    }
}
