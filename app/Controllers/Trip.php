<?php

namespace App\Controllers;

use App\Libraries\ActivityLogger;
use App\Models\Port;
use App\Models\Ship;
use App\Models\Trip as TripModel;
use App\Models\TripRequest;
use CodeIgniter\Exceptions\PageNotFoundException;

class Trip extends BaseController
{
    protected TripModel $tripModel;
    protected Ship $shipModel;
    protected Port $portModel;

    public function __construct()
    {
        $this->tripModel = new TripModel();
        $this->shipModel = new Ship();
        $this->portModel = new Port();
    }

    public function index()
    {
        $filters = $this->filters();
        $filteredCount = $this->filteredQuery($filters)->countAllResults();
        $builder = $this->filteredQuery($filters);
        $trips = $builder->orderBy('trips.departure_date', 'DESC')->paginate(18);
        $trips = array_map(fn (array $trip): array => $this->decorateTrip($trip), $trips);
        $now = date('Y-m-d H:i:s');

        return view('trips/index', [
            'trips' => $trips,
            'pager' => $builder->pager,
            'ships' => (new Ship())->orderBy('name', 'ASC')->findAll(),
            'filters' => $filters,
            'stats' => [
                'total_trips' => (new TripModel())->countAllResults(),
                'filtered_trips' => $filteredCount,
                'upcoming' => (new TripModel())->where('departure_date >', $now)->countAllResults(),
                'active' => (new TripModel())->where('departure_date <=', $now)->where('arrival_date >=', $now)->countAllResults(),
                'completed' => (new TripModel())->where('arrival_date <', $now)->countAllResults(),
            ],
        ]);
    }

    public function export()
    {
        $trips = $this->filteredQuery($this->filters())
            ->orderBy('trips.departure_date', 'DESC')
            ->findAll(5000);

        $output = fopen('php://temp', 'w+');
        if ($output === false) {
            return redirect()->to(site_url('trips'))->with('error', lang('App.msg_operation_failed'));
        }
        fwrite($output, "\xEF\xBB\xBF");
        fputcsv($output, [
            lang('App.id'), lang('App.ship'), lang('App.departure_port'), lang('App.arrival_port'),
            lang('App.departure_date'), lang('App.arrival_date'), lang('App.trip_status'), lang('App.duration_hours'),
        ], ',', '"', '');

        foreach ($trips as $trip) {
            $trip = $this->decorateTrip($trip);
            fputcsv($output, [
                $trip['id'],
                $this->safeCsvCell((string) ($trip['ship_name'] ?? '')),
                $this->safeCsvCell((string) ($trip['departure_port_name'] ?? '')),
                $this->safeCsvCell((string) ($trip['arrival_port_name'] ?? '')),
                $trip['departure_date'], $trip['arrival_date'],
                $this->safeCsvCell((string) ($trip['status_label'] ?? '')),
                $trip['duration_hours'],
            ], ',', '"', '');
        }

        rewind($output);
        $csv = stream_get_contents($output);
        fclose($output);

        return $this->response
            ->setHeader('Content-Type', 'text/csv; charset=utf-8')
            ->setHeader('Content-Disposition', 'attachment; filename="trips-' . date('Ymd-His') . '.csv"')
            ->setHeader('X-Content-Type-Options', 'nosniff')
            ->setBody((string) $csv);
    }

    public function create()
    {
        $ships = $this->shipModel->orderBy('name', 'ASC')->findAll();
        $ports = $this->portModel->orderBy('name', 'ASC')->findAll();

        if ($ships === [] || count($ports) < 2) {
            return redirect()->to(site_url('trips'))->with('error', lang('App.msg_trip_prerequisites'));
        }

        return view('trips/create', [
            'ships' => $ships,
            'ports' => $ports,
            'shipSchedules' => $this->shipSchedules(),
        ]);
    }

    public function store()
    {
        $data = $this->payload();
        if ($error = $this->scheduleError($data)) {
            return redirect()->back()->withInput()->with('error', $error);
        }

        if (! $this->tripModel->insert($data)) {
            return redirect()->back()->withInput()->with('errors', $this->tripModel->errors());
        }

        $id = (int) $this->tripModel->getInsertID();
        (new ActivityLogger())->log('trip.created', sprintf(lang('App.activity_trip_created'), $id), 'trip', $id, $data);
        return redirect()->to(site_url('trips'))->with('success', lang('App.msg_trip_created'));
    }

    public function edit(int $id)
    {
        $trip = $this->tripModel->find($id);
        if (! $trip) {
            throw PageNotFoundException::forPageNotFound(lang('App.msg_trip_not_found'));
        }
        if (strtotime((string) ($trip['departure_date'] ?? '')) <= time()) {
            return redirect()->to(site_url('trips/show/' . $id))->with('error', lang('App.msg_trip_started_cannot_edit'));
        }

        return view('trips/edit', [
            'trip' => $trip,
            'ships' => $this->shipModel->orderBy('name', 'ASC')->findAll(),
            'ports' => $this->portModel->orderBy('name', 'ASC')->findAll(),
            'shipSchedules' => $this->shipSchedules($id),
        ]);
    }

    public function update(int $id)
    {
        $trip = $this->tripModel->find($id);
        if (! $trip) {
            return redirect()->to(site_url('trips'))->with('error', lang('App.msg_trip_not_found'));
        }
        if (strtotime((string) ($trip['departure_date'] ?? '')) <= time()) {
            return redirect()->to(site_url('trips/show/' . $id))->with('error', lang('App.msg_trip_started_cannot_edit'));
        }

        $data = $this->payload();
        if ($error = $this->scheduleError($data, $id)) {
            return redirect()->back()->withInput()->with('error', $error);
        }

        if (! $this->tripModel->update($id, $data)) {
            return redirect()->back()->withInput()->with('errors', $this->tripModel->errors());
        }

        (new ActivityLogger())->log('trip.updated', sprintf(lang('App.activity_trip_updated'), $id), 'trip', $id, $data);
        return redirect()->to(site_url('trips'))->with('success', lang('App.msg_trip_updated'));
    }

    public function delete(int $id)
    {
        $trip = $this->tripModel->find($id);
        if (! $trip) {
            return redirect()->to(site_url('trips'))->with('error', lang('App.msg_trip_not_found'));
        }

        if (strtotime((string) ($trip['departure_date'] ?? '')) <= time()) {
            return redirect()->to(site_url('trips'))->with('error', lang('App.msg_trip_started_cannot_delete'));
        }

        $requestCount = (new TripRequest())->where('trip_id', $id)->countAllResults();
        if ($requestCount > 0) {
            return redirect()->to(site_url('trips'))->with('error', lang('App.msg_trip_has_requests'));
        }

        if (! $this->tripModel->delete($id)) {
            return redirect()->back()->with('error', lang('App.msg_trip_delete_failed'));
        }
        (new ActivityLogger())->log('trip.deleted', sprintf(lang('App.activity_trip_deleted'), $id), 'trip', $id);
        return redirect()->to(site_url('trips'))->with('success', lang('App.msg_trip_deleted'));
    }

    public function show(int $id)
    {
        $trip = (new TripModel())
            ->select('trips.*, ships.name AS ship_name, ships.type AS ship_type, ships.capacity AS ship_capacity, departure_ports.name AS departure_port_name, departure_ports.country AS departure_port_country, departure_ports.city AS departure_port_city, departure_ports.latitude AS dep_lat, departure_ports.longitude AS dep_lng, arrival_ports.name AS arrival_port_name, arrival_ports.country AS arrival_port_country, arrival_ports.city AS arrival_port_city, arrival_ports.latitude AS arr_lat, arrival_ports.longitude AS arr_lng')
            ->join('ships', 'ships.id = trips.ship_id')
            ->join('ports AS departure_ports', 'departure_ports.id = trips.departure_port_id')
            ->join('ports AS arrival_ports', 'arrival_ports.id = trips.arrival_port_id')
            ->find($id);

        if (! $trip) {
            throw PageNotFoundException::forPageNotFound(lang('App.msg_trip_not_found'));
        }

        $approvedPeopleCount = $this->approvedPeopleCount($id);
        $tripCapacity = max(0, (int) ($trip['ship_capacity'] ?? 0));
        $remainingCapacity = max(0, $tripCapacity - $approvedPeopleCount);
        $isTripFull = $tripCapacity > 0 && $approvedPeopleCount >= $tripCapacity;

        $existingRequest = null;
        $overlappingApprovedTrip = null;
        if (! is_admin_user()) {
            $userId = (int) session()->get('userId');
            $existingRequest = (new TripRequest())
                ->where('trip_id', $id)
                ->where('user_id', $userId)
                ->whereIn('status', ['pending', 'approved'])
                ->orderBy('created_at', 'DESC')
                ->first();

            $overlappingApprovedTrip = $this->overlappingApprovedTripForUser($userId, $trip);
        }

        return view('trips/show', [
            'trip' => $this->decorateTrip($trip),
            'existingRequest' => $existingRequest,
            'overlappingApprovedTrip' => $overlappingApprovedTrip,
            'capacityStatus' => [
                'capacity' => $tripCapacity,
                'approved' => $approvedPeopleCount,
                'remaining' => $remainingCapacity,
                'is_full' => $isTripFull,
            ],
        ]);
    }


    private function approvedPeopleCount(int $tripId): int
    {
        if ($tripId < 1) {
            return 0;
        }

        $db = db_connect();
        if ($db->fieldExists('people_count', 'trip_requests')) {
            $row = $db->table('trip_requests')
                ->select('COALESCE(SUM(COALESCE(people_count, 1)), 0) AS total', false)
                ->where('trip_id', $tripId)
                ->where('status', 'approved')
                ->get()
                ->getRowArray();

            return max(0, (int) ($row['total'] ?? 0));
        }

        return (int) (new TripRequest())
            ->where('trip_id', $tripId)
            ->where('status', 'approved')
            ->countAllResults();
    }

    private function overlappingApprovedTripForUser(int $userId, array $trip): ?array
    {
        if ($userId < 1 || empty($trip['departure_date']) || empty($trip['arrival_date'])) {
            return null;
        }

        return (new TripRequest())
            ->select('trip_requests.id AS request_id, trips.id AS conflict_trip_id, trips.departure_date, trips.arrival_date, ships.name AS ship_name, departure_ports.name AS departure_port_name, arrival_ports.name AS arrival_port_name')
            ->join('trips', 'trips.id = trip_requests.trip_id')
            ->join('ships', 'ships.id = trips.ship_id')
            ->join('ports AS departure_ports', 'departure_ports.id = trips.departure_port_id')
            ->join('ports AS arrival_ports', 'arrival_ports.id = trips.arrival_port_id')
            ->where('trip_requests.user_id', $userId)
            ->where('trip_requests.status', 'approved')
            ->where('trips.id !=', (int) ($trip['id'] ?? 0))
            ->where('trips.departure_date <', (string) $trip['arrival_date'])
            ->where('trips.arrival_date >', (string) $trip['departure_date'])
            ->orderBy('trips.departure_date', 'ASC')
            ->first();
    }

    private function filters(): array
    {
        return [
            'q' => trim((string) $this->request->getGet('q')),
            'ship_id' => (int) $this->request->getGet('ship_id'),
            'status' => trim((string) $this->request->getGet('status')),
            'from_date' => trim((string) $this->request->getGet('from_date')),
            'to_date' => trim((string) $this->request->getGet('to_date')),
        ];
    }

    private function filteredQuery(array $filters): TripModel
    {
        $builder = new TripModel();
        $builder->select('trips.*, ships.name AS ship_name, departure_ports.name AS departure_port_name, arrival_ports.name AS arrival_port_name')
            ->join('ships', 'ships.id = trips.ship_id')
            ->join('ports AS departure_ports', 'departure_ports.id = trips.departure_port_id')
            ->join('ports AS arrival_ports', 'arrival_ports.id = trips.arrival_port_id');

        if ($filters['q'] !== '') {
            $builder->groupStart()
                ->like('ships.name', $filters['q'])
                ->orLike('departure_ports.name', $filters['q'])
                ->orLike('arrival_ports.name', $filters['q'])
                ->groupEnd();
        }
        if ($filters['ship_id'] > 0) {
            $builder->where('trips.ship_id', $filters['ship_id']);
        }
        if ($filters['from_date'] !== '') {
            $builder->where('trips.departure_date >=', $filters['from_date'] . ' 00:00:00');
        }
        if ($filters['to_date'] !== '') {
            $builder->where('trips.departure_date <=', $filters['to_date'] . ' 23:59:59');
        }

        $now = date('Y-m-d H:i:s');
        if ($filters['status'] === 'upcoming') {
            $builder->where('trips.departure_date >', $now);
        } elseif ($filters['status'] === 'active') {
            $builder->where('trips.departure_date <=', $now)->where('trips.arrival_date >=', $now);
        } elseif ($filters['status'] === 'completed') {
            $builder->where('trips.arrival_date <', $now);
        }

        return $builder;
    }

    private function payload(): array
    {
        return [
            'ship_id' => (int) $this->request->getPost('ship_id'),
            'departure_port_id' => (int) $this->request->getPost('departure_port_id'),
            'arrival_port_id' => (int) $this->request->getPost('arrival_port_id'),
            'departure_date' => $this->normalizeDateTime((string) $this->request->getPost('departure_date')),
            'arrival_date' => $this->normalizeDateTime((string) $this->request->getPost('arrival_date')),
        ];
    }


    private function normalizeDateTime(string $value): string
    {
        $value = trim($value);
        if ($value === '') {
            return '';
        }

        $timestamp = strtotime($value);
        return $timestamp === false ? $value : date('Y-m-d H:i:s', $timestamp);
    }


    /**
     * Upcoming and currently active reservations used by the trip form to block
     * ships that cannot accept another voyage in the selected date window.
     */
    private function shipSchedules(?int $ignoreTripId = null): array
    {
        $builder = (new TripModel())
            ->select('trips.id, trips.ship_id, trips.departure_date, trips.arrival_date, departure_ports.name AS departure_port_name, arrival_ports.name AS arrival_port_name')
            ->join('ports AS departure_ports', 'departure_ports.id = trips.departure_port_id', 'left')
            ->join('ports AS arrival_ports', 'arrival_ports.id = trips.arrival_port_id', 'left')
            ->where('trips.arrival_date >=', date('Y-m-d H:i:s'))
            ->orderBy('trips.departure_date', 'ASC');

        if ($ignoreTripId !== null) {
            $builder->where('trips.id !=', $ignoreTripId);
        }

        return array_map(static function (array $trip): array {
            return [
                'id' => (int) ($trip['id'] ?? 0),
                'ship_id' => (int) ($trip['ship_id'] ?? 0),
                'departure_date' => (string) ($trip['departure_date'] ?? ''),
                'arrival_date' => (string) ($trip['arrival_date'] ?? ''),
                'departure_port_name' => (string) ($trip['departure_port_name'] ?? ''),
                'arrival_port_name' => (string) ($trip['arrival_port_name'] ?? ''),
            ];
        }, $builder->findAll());
    }

    private function scheduleError(array $data, ?int $ignoreId = null): ?string
    {
        if (! $this->validateData($data, $this->tripModel->getValidationRules())) {
            session()->setFlashdata('errors', $this->validator->getErrors());
            return lang('App.msg_validation_failed');
        }

        if ((int) $data['departure_port_id'] === (int) $data['arrival_port_id']) {
            return lang('App.msg_different_ports');
        }

        $departure = strtotime($data['departure_date']);
        $arrival = strtotime($data['arrival_date']);
        if ($departure === false || $arrival === false || $arrival <= $departure) {
            return lang('App.msg_arrival_after_departure');
        }
        if ($departure <= time()) {
            return lang('App.msg_departure_must_be_future');
        }

        $overlap = (new TripModel())
            ->where('ship_id', $data['ship_id'])
            ->where('departure_date <', $data['arrival_date'])
            ->where('arrival_date >', $data['departure_date']);
        if ($ignoreId !== null) {
            $overlap->where('id !=', $ignoreId);
        }

        return $overlap->countAllResults() > 0 ? lang('App.msg_ship_schedule_conflict') : null;
    }

    private function safeCsvCell(string $value): string
    {
        return preg_match('/^[=+\-@]/u', ltrim($value)) === 1 ? "'" . $value : $value;
    }

    private function decorateTrip(array $trip): array
    {
        $departureTs = strtotime((string) $trip['departure_date']) ?: 0;
        $arrivalTs = strtotime((string) $trip['arrival_date']) ?: 0;
        $now = time();

        if ($departureTs > $now) {
            [$statusKey, $statusClass] = ['trip_status_upcoming', 'info'];
        } elseif ($arrivalTs < $now) {
            [$statusKey, $statusClass] = ['trip_status_completed', 'success'];
        } else {
            [$statusKey, $statusClass] = ['trip_status_active', 'warning'];
        }

        $trip['status_key'] = $statusKey;
        $trip['status_label'] = lang('App.' . $statusKey);
        $trip['status_class'] = $statusClass;
        $trip['duration_hours'] = max(0, round(($arrivalTs - $departureTs) / 3600, 1));
        return $trip;
    }
}
