<?php

namespace App\Controllers;

use App\Models\ActivityLog;
use App\Models\ContactMessage;
use App\Models\Port;
use App\Models\Ship;
use App\Models\Trip;
use App\Models\TripRequest;

class Dashboard extends BaseController
{
    public function index()
    {
        return is_admin_user() ? $this->adminDashboard() : $this->userDashboard();
    }

    private function adminDashboard()
    {
        $now = date('Y-m-d H:i:s');
        $totalPorts = (new Port())->countAllResults();
        $totalShips = (new Ship())->countAllResults();
        $totalTrips = (new Trip())->countAllResults();
        $upcomingTrips = (new Trip())->where('departure_date >', $now)->countAllResults();
        $activeTrips = (new Trip())->where('departure_date <=', $now)->where('arrival_date >=', $now)->countAllResults();
        $completedTrips = (new Trip())->where('arrival_date <', $now)->countAllResults();
        $pendingRequests = (new TripRequest())->where('status', 'pending')->countAllResults();
        $capacity = (new Ship())->selectSum('capacity')->first();

        $upcomingDepartures = (new Trip())
            ->select('trips.id, trips.departure_date, trips.arrival_date, ships.name AS ship_name, departure_ports.name AS departure_port_name, arrival_ports.name AS arrival_port_name')
            ->join('ships', 'ships.id = trips.ship_id')
            ->join('ports AS departure_ports', 'departure_ports.id = trips.departure_port_id')
            ->join('ports AS arrival_ports', 'arrival_ports.id = trips.arrival_port_id')
            ->where('trips.departure_date >=', $now)
            ->orderBy('trips.departure_date', 'ASC')
            ->findAll(7);

        $topRoutes = (new Trip())
            ->select('departure_ports.name AS departure_port_name, arrival_ports.name AS arrival_port_name, COUNT(trips.id) AS total_trips')
            ->join('ports AS departure_ports', 'departure_ports.id = trips.departure_port_id')
            ->join('ports AS arrival_ports', 'arrival_ports.id = trips.arrival_port_id')
            ->groupBy('trips.departure_port_id, trips.arrival_port_id, departure_ports.name, arrival_ports.name')
            ->orderBy('total_trips', 'DESC')
            ->findAll(6);

        [$monthLabels, $monthCounts, $durationSamples] = $this->monthlyTripMetrics();
        $completedWindow = $activeTrips + $completedTrips;

        $recentActivity = [];
        try {
            $recentActivity = (new ActivityLog())
                ->select('activity_logs.*, users.email AS user_email, users.full_name AS user_name')
                ->join('users', 'users.id = activity_logs.user_id', 'left')
                ->orderBy('activity_logs.created_at', 'DESC')
                ->findAll(8);
        } catch (\Throwable $e) {
            log_message('debug', 'Activity log unavailable: {message}', ['message' => $e->getMessage()]);
        }

        $latestRequests = (new TripRequest())
            ->select('trip_requests.id, trip_requests.status, trip_requests.created_at, users.email AS user_email, users.full_name AS user_name, ships.name AS ship_name')
            ->join('users', 'users.id = trip_requests.user_id')
            ->join('trips', 'trips.id = trip_requests.trip_id')
            ->join('ships', 'ships.id = trips.ship_id')
            ->orderBy('trip_requests.created_at', 'DESC')
            ->findAll(5);

        $unreadMessages = 0;
        try {
            $unreadMessages = (new ContactMessage())->where('is_read', 0)->countAllResults();
        } catch (\Throwable $e) {
            // Migration may not have run yet.
        }

        return view('dashboard/index', [
            'isAdminDashboard' => true,
            'metrics' => [
                'totalPorts' => $totalPorts,
                'totalShips' => $totalShips,
                'totalTrips' => $totalTrips,
                'upcomingTrips' => $upcomingTrips,
                'activeTrips' => $activeTrips,
                'completedTrips' => $completedTrips,
                'totalCapacity' => (int) ($capacity['capacity'] ?? 0),
                'pendingRequests' => $pendingRequests,
                'unreadMessages' => $unreadMessages,
            ],
            'upcomingDepartures' => $upcomingDepartures,
            'topRoutes' => $topRoutes,
            'latestRequests' => $latestRequests,
            'recentActivity' => $recentActivity,
            'chartTripsByMonth' => ['labels' => $monthLabels, 'values' => array_values($monthCounts)],
            'chartTripsByStatus' => [
                'labels' => [lang('App.trip_status_upcoming'), lang('App.trip_status_active'), lang('App.trip_status_completed')],
                'values' => [$upcomingTrips, $activeTrips, $completedTrips],
            ],
            'insights' => [
                'fleetUtilization' => $totalShips > 0 ? min(100, round(($activeTrips / $totalShips) * 100, 1)) : 0,
                'completionRate' => $completedWindow > 0 ? round(($completedTrips / $completedWindow) * 100, 1) : 0,
                'avgDurationHours' => $durationSamples !== [] ? round(array_sum($durationSamples) / count($durationSamples), 1) : 0,
            ],
        ]);
    }

    private function userDashboard()
    {
        $now = date('Y-m-d H:i:s');
        $userId = (int) session()->get('userId');
        $filters = [
            'ship_name' => trim((string) $this->request->getGet('ship_name')),
            'port_name' => trim((string) $this->request->getGet('port_name')),
            'date' => trim((string) $this->request->getGet('date')),
            'status' => trim((string) $this->request->getGet('status')),
        ];

        $builder = (new Trip())
            ->select('trips.*, ships.name AS ship_name, ships.type AS ship_type, ships.capacity AS ship_capacity, departure_ports.name AS departure_port_name, arrival_ports.name AS arrival_port_name')
            ->join('ships', 'ships.id = trips.ship_id')
            ->join('ports AS departure_ports', 'departure_ports.id = trips.departure_port_id')
            ->join('ports AS arrival_ports', 'arrival_ports.id = trips.arrival_port_id');

        if ($filters['ship_name'] !== '') {
            $builder->like('ships.name', $filters['ship_name']);
        }
        if ($filters['port_name'] !== '') {
            $builder->groupStart()->like('departure_ports.name', $filters['port_name'])->orLike('arrival_ports.name', $filters['port_name'])->groupEnd();
        }
        if ($filters['date'] !== '') {
            $date = \DateTimeImmutable::createFromFormat('!Y-m-d', $filters['date']);
            if ($date !== false) {
                $builder
                    ->where('trips.departure_date >=', $date->format('Y-m-d 00:00:00'))
                    ->where('trips.departure_date <', $date->modify('+1 day')->format('Y-m-d 00:00:00'));
            }
        }

        if ($filters['status'] === 'active') {
            $builder->where('trips.departure_date <=', $now)->where('trips.arrival_date >=', $now);
        } elseif ($filters['status'] === 'completed') {
            $builder->where('trips.arrival_date <', $now);
        } else {
            $builder->where('trips.departure_date >', $now);
        }

        $availableTrips = array_map(
            fn (array $trip): array => $this->decorateTrip($trip),
            $builder->orderBy('trips.departure_date', 'ASC')->findAll(8)
        );

        $myRequests = (new TripRequest())
            ->select('trip_requests.*, ships.name AS ship_name, departure_ports.name AS departure_port_name, arrival_ports.name AS arrival_port_name, trips.departure_date, trips.arrival_date')
            ->join('trips', 'trips.id = trip_requests.trip_id')
            ->join('ships', 'ships.id = trips.ship_id')
            ->join('ports AS departure_ports', 'departure_ports.id = trips.departure_port_id')
            ->join('ports AS arrival_ports', 'arrival_ports.id = trips.arrival_port_id')
            ->where('trip_requests.user_id', $userId)
            ->orderBy('trip_requests.created_at', 'DESC')
            ->findAll(6);

        return view('dashboard/index', [
            'isAdminDashboard' => false,
            'metrics' => [
                'totalTrips' => (new Trip())->countAllResults(),
                'activeTrips' => (new Trip())->where('departure_date <=', $now)->where('arrival_date >=', $now)->countAllResults(),
                'upcomingTrips' => (new Trip())->where('departure_date >', $now)->countAllResults(),
                'pendingRequests' => (new TripRequest())->where('user_id', $userId)->where('status', 'pending')->countAllResults(),
                'approvedRequests' => (new TripRequest())->where('user_id', $userId)->where('status', 'approved')->countAllResults(),
            ],
            'userDashboard' => [
                'availableTrips' => $availableTrips,
                'myRequests' => $myRequests,
                'filters' => $filters,
            ],
        ]);
    }

    private function monthlyTripMetrics(): array
    {
        $labels = [];
        $counts = [];
        for ($i = 5; $i >= 0; $i--) {
            $timestamp = strtotime("first day of -{$i} month");
            $key = date('Y-m', $timestamp);
            $labels[] = localized_month_year($timestamp);
            $counts[$key] = 0;
        }

        $trips = (new Trip())
            ->select('departure_date, arrival_date')
            ->where('departure_date >=', date('Y-m-01 00:00:00', strtotime('first day of -5 months')))
            ->findAll();

        $durations = [];
        foreach ($trips as $trip) {
            $departure = strtotime((string) ($trip['departure_date'] ?? ''));
            $arrival = strtotime((string) ($trip['arrival_date'] ?? ''));
            if ($departure !== false) {
                $key = date('Y-m', $departure);
                if (array_key_exists($key, $counts)) {
                    $counts[$key]++;
                }
            }
            if ($departure !== false && $arrival !== false && $arrival > $departure) {
                $durations[] = ($arrival - $departure) / 3600;
            }
        }

        return [$labels, $counts, $durations];
    }

    private function decorateTrip(array $trip): array
    {
        $departure = strtotime((string) $trip['departure_date']) ?: 0;
        $arrival = strtotime((string) $trip['arrival_date']) ?: 0;
        if ($departure > time()) {
            [$key, $class] = ['trip_status_upcoming', 'info'];
        } elseif ($arrival < time()) {
            [$key, $class] = ['trip_status_completed', 'success'];
        } else {
            [$key, $class] = ['trip_status_active', 'warning'];
        }

        $trip['status_label'] = lang('App.' . $key);
        $trip['status_class'] = $class;
        return $trip;
    }
}
