<?php

namespace App\Controllers;

use App\Models\TripRequest;

class MyTrips extends BaseController
{
    protected TripRequest $tripRequestModel;

    public function __construct()
    {
        $this->tripRequestModel = new TripRequest();
    }

    public function index()
    {
        $userId = (int) (session()->get('userId') ?? 0);
        $status = trim((string) $this->request->getGet('status'));
        $q = trim((string) $this->request->getGet('q'));

        $model = new TripRequest();
        $builder = $this->baseQuery($model, $userId);

        if (in_array($status, ['approved', 'pending', 'rejected', 'cancelled'], true)) {
            $builder->where('trip_requests.status', $status);
        }
        if ($q !== '') {
            $builder->groupStart()
                ->like('ships.name', $q)
                ->orLike('departure_ports.name', $q)
                ->orLike('arrival_ports.name', $q)
                ->groupEnd();
        }

        return view('my_trips/index', [
            'myTrips' => $builder->orderBy('trips.departure_date', 'DESC')->paginate(20),
            'pager' => $model->pager,
            'filters' => ['status' => $status, 'q' => $q],
            'stats' => [
                'booked' => (new TripRequest())->where('user_id', $userId)->where('status', 'approved')->countAllResults(),
                'pending' => (new TripRequest())->where('user_id', $userId)->where('status', 'pending')->countAllResults(),
                'rejected' => (new TripRequest())->where('user_id', $userId)->where('status', 'rejected')->countAllResults(),
            ],
        ]);
    }

    public function downloadCsv()
    {
        if (! can('download_my_trips')) {
            return redirect()->to(site_url('dashboard'))->with('error', lang('App.msg_permission_denied'));
        }

        $userId = (int) (session()->get('userId') ?? 0);
        $myTrips = $this->baseQuery(new TripRequest(), $userId)
            ->orderBy('trips.departure_date', 'ASC')
            ->findAll();

        $filename = 'my-trips-' . date('Ymd-His') . '.csv';
        $output = fopen('php://temp', 'w+');
        if ($output === false) {
            return redirect()->to(site_url('my-trips'))->with('error', lang('App.msg_operation_failed'));
        }

        // UTF-8 BOM keeps Arabic text readable in spreadsheet software.
        fwrite($output, "\xEF\xBB\xBF");
        fputcsv($output, [
            lang('App.ship'),
            lang('App.departure_port'),
            lang('App.arrival_port'),
            lang('App.departure_date'),
            lang('App.arrival_date'),
            lang('App.request_type'),
            lang('App.request_status'),
        ], ',', '"', '');

        foreach ($myTrips as $trip) {
            fputcsv($output, array_map([$this, 'safeCsvCell'], [
                (string) ($trip['ship_name'] ?? ''),
                (string) ($trip['departure_port_name'] ?? ''),
                (string) ($trip['arrival_port_name'] ?? ''),
                (string) ($trip['departure_date'] ?? ''),
                (string) ($trip['arrival_date'] ?? ''),
                request_type_label($trip['request_type'] ?? null),
                request_status_label($trip['status'] ?? null),
            ]), ',', '"', '');
        }

        rewind($output);
        $csv = (string) stream_get_contents($output);
        fclose($output);

        return $this->response
            ->setHeader('Content-Type', 'text/csv; charset=utf-8')
            ->setHeader('Content-Disposition', 'attachment; filename="' . $filename . '"')
            ->setHeader('X-Content-Type-Options', 'nosniff')
            ->setBody($csv);
    }

    private function baseQuery(TripRequest $model, int $userId): TripRequest
    {
        return $model
            ->select('trip_requests.*, ships.name AS ship_name, departure_ports.name AS departure_port_name, arrival_ports.name AS arrival_port_name, trips.departure_date, trips.arrival_date')
            ->join('trips', 'trips.id = trip_requests.trip_id')
            ->join('ships', 'ships.id = trips.ship_id')
            ->join('ports AS departure_ports', 'departure_ports.id = trips.departure_port_id')
            ->join('ports AS arrival_ports', 'arrival_ports.id = trips.arrival_port_id')
            ->where('trip_requests.user_id', $userId);
    }

    private function safeCsvCell(string $value): string
    {
        $value = trim($value);
        return preg_match('/^[=+\-@]/u', $value) === 1 ? "'" . $value : $value;
    }
}
