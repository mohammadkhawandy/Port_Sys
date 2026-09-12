<?php

namespace App\Controllers;

use App\Libraries\ActivityLogger;
use App\Models\Notification;
use App\Models\Trip;
use App\Models\TripRequest as TripRequestModel;
use App\Models\User;

class TripRequest extends BaseController
{
    protected TripRequestModel $tripRequestModel;
    protected Trip $tripModel;
    protected Notification $notificationModel;

    public function __construct()
    {
        $this->tripRequestModel = new TripRequestModel();
        $this->tripModel = new Trip();
        $this->notificationModel = new Notification();
    }

    public function create()
    {
        if (is_admin_user()) {
            return redirect()->to(site_url('trips'))->with('error', lang('App.msg_permission_denied'));
        }

        $userId = (int) session()->get('userId');
        $tripId = (int) $this->request->getPost('trip_id');
        $trip = $this->tripModel->find($tripId);

        if (! $trip) {
            return redirect()->to(site_url('trips'))->with('error', lang('App.msg_trip_not_found'));
        }
        if (strtotime((string) $trip['departure_date']) <= time()) {
            return redirect()->to(site_url('trips/show/' . $tripId))->with('error', lang('App.requests_closed'));
        }

        $existing = (new TripRequestModel())
            ->where('user_id', $userId)
            ->where('trip_id', $tripId)
            ->whereIn('status', ['pending', 'approved'])
            ->first();
        if ($existing) {
            return redirect()->to(site_url('trip-requests/my'))->with('error', lang('App.msg_duplicate_trip_request'));
        }

        if ($this->userHasOverlappingApprovedTrip($userId, $trip)) {
            return redirect()->to(site_url('trips/show/' . $tripId))->with('error', lang('App.msg_user_has_overlapping_approved_trip'));
        }

        $peopleCount = max(1, (int) $this->request->getPost('people_count'));
        $capacityStatus = $this->tripCapacityStatus($tripId);
        if ($capacityStatus['capacity'] > 0 && $capacityStatus['remaining'] < 1) {
            return redirect()->to(site_url('trips/show/' . $tripId))->with('error', lang('App.msg_trip_full'));
        }
        if ($capacityStatus['capacity'] > 0 && $peopleCount > $capacityStatus['remaining']) {
            return redirect()->back()->withInput()->with('error', sprintf(lang('App.msg_people_count_exceeds_remaining'), $capacityStatus['remaining']));
        }

        $data = [
            'user_id' => $userId,
            'trip_id' => $tripId,
            'request_type' => (string) $this->request->getPost('request_type'),
            'people_count' => $peopleCount,
            'contact_name' => trim((string) $this->request->getPost('contact_name')),
            'contact_phone' => trim((string) $this->request->getPost('contact_phone')),
            'contact_email' => mb_strtolower(trim((string) $this->request->getPost('contact_email'))),
            'organization' => trim((string) $this->request->getPost('organization')),
            'message' => trim((string) $this->request->getPost('message')),
            'status' => 'pending',
        ];

        if (! $this->tripRequestModel->insert($data)) {
            return redirect()->back()->withInput()->with('errors', $this->tripRequestModel->errors());
        }

        $requestId = (int) $this->tripRequestModel->getInsertID();
        $this->notifyAdmins(
            lang('App.notification_new_request_title'),
            lang('App.notification_new_request_message'),
            $requestId
        );
        (new ActivityLogger())->log('trip_request.created', sprintf(lang('App.activity_trip_request_created'), $requestId), 'trip_request', $requestId);

        return redirect()->to(site_url('trip-requests/my'))->with('success', lang('App.msg_trip_request_created'));
    }

    public function myRequests()
    {
        $userId = (int) session()->get('userId');
        $status = trim((string) $this->request->getGet('status'));
        $model = new TripRequestModel();
        $builder = $model
            ->select('trip_requests.*, ships.name AS ship_name, departure_ports.name AS departure_port_name, arrival_ports.name AS arrival_port_name, trips.departure_date, trips.arrival_date')
            ->join('trips', 'trips.id = trip_requests.trip_id')
            ->join('ships', 'ships.id = trips.ship_id')
            ->join('ports AS departure_ports', 'departure_ports.id = trips.departure_port_id')
            ->join('ports AS arrival_ports', 'arrival_ports.id = trips.arrival_port_id')
            ->where('trip_requests.user_id', $userId);

        if (in_array($status, ['pending', 'approved', 'rejected', 'cancelled'], true)) {
            $builder->where('trip_requests.status', $status);
        }

        return view('trip_requests/my_requests', [
            'requests' => $builder->orderBy('trip_requests.created_at', 'DESC')->paginate(20),
            'pager' => $model->pager,
            'filterStatus' => $status,
        ]);
    }

    public function cancel(int $id)
    {
        $userId = (int) session()->get('userId');
        $request = (new TripRequestModel())
            ->where('id', $id)
            ->where('user_id', $userId)
            ->first();

        if (! $request) {
            return redirect()->to(site_url('trip-requests/my'))->with('error', lang('App.msg_trip_request_not_found'));
        }
        if (($request['status'] ?? '') !== 'pending') {
            return redirect()->to(site_url('trip-requests/my'))->with('error', lang('App.msg_request_cannot_cancel'));
        }

        $db = db_connect();
        $updated = $db->table('trip_requests')
            ->where('id', $id)
            ->where('user_id', $userId)
            ->where('status', 'pending')
            ->update(['status' => 'cancelled', 'updated_at' => date('Y-m-d H:i:s')]);
        if (! $updated || $db->affectedRows() !== 1) {
            return redirect()->to(site_url('trip-requests/my'))->with('error', lang('App.msg_operation_failed'));
        }

        $this->notifyAdmins(
            lang('App.notification_request_cancelled_title'),
            lang('App.notification_request_cancelled_message'),
            $id
        );
        (new ActivityLogger())->log('trip_request.cancelled', sprintf(lang('App.activity_trip_request_cancelled'), $id), 'trip_request', $id);
        return redirect()->to(site_url('trip-requests/my'))->with('success', lang('App.msg_trip_request_cancelled'));
    }

    public function index()
    {
        $status = trim((string) $this->request->getGet('status'));
        $q = trim((string) $this->request->getGet('q'));
        $model = new TripRequestModel();
        $builder = $model
            ->select('trip_requests.*, users.email AS user_email, users.full_name AS user_name, ships.name AS ship_name, departure_ports.name AS departure_port_name, arrival_ports.name AS arrival_port_name, trips.departure_date')
            ->join('users', 'users.id = trip_requests.user_id')
            ->join('trips', 'trips.id = trip_requests.trip_id')
            ->join('ships', 'ships.id = trips.ship_id')
            ->join('ports AS departure_ports', 'departure_ports.id = trips.departure_port_id')
            ->join('ports AS arrival_ports', 'arrival_ports.id = trips.arrival_port_id');

        if (in_array($status, ['pending', 'approved', 'rejected', 'cancelled'], true)) {
            $builder->where('trip_requests.status', $status);
        }
        if ($q !== '') {
            $builder->groupStart()
                ->like('users.email', $q)
                ->orLike('users.full_name', $q)
                ->orLike('trip_requests.contact_name', $q)
                ->orLike('trip_requests.contact_phone', $q)
                ->orLike('trip_requests.contact_email', $q)
                ->orLike('trip_requests.organization', $q)
                ->orLike('ships.name', $q)
                ->groupEnd();
        }

        return view('trip_requests/index', [
            'requests' => $builder->orderBy('trip_requests.created_at', 'DESC')->paginate(20),
            'pager' => $model->pager,
            'filters' => ['status' => $status, 'q' => $q],
            'stats' => [
                'pending' => (new TripRequestModel())->where('status', 'pending')->countAllResults(),
                'approved' => (new TripRequestModel())->where('status', 'approved')->countAllResults(),
                'rejected' => (new TripRequestModel())->where('status', 'rejected')->countAllResults(),
            ],
        ]);
    }

    public function acceptedForTrip(int $tripId)
    {
        $trip = $this->tripModel->find($tripId);
        if (! $trip) {
            return redirect()->to(site_url('trips'))->with('error', lang('App.msg_trip_not_found'));
        }

        $requests = (new TripRequestModel())
            ->select('trip_requests.*, users.email AS user_email, users.full_name AS user_name')
            ->join('users', 'users.id = trip_requests.user_id')
            ->where('trip_requests.trip_id', $tripId)
            ->where('trip_requests.status', 'approved')
            ->orderBy('trip_requests.created_at', 'DESC')
            ->findAll();

        return view('trip_requests/accepted', ['requests' => $requests, 'tripId' => $tripId]);
    }

    public function updateStatus(int $id, string $status)
    {
        if (! in_array($status, ['approved', 'rejected'], true)) {
            return redirect()->to(site_url('trip-requests'))->with('error', lang('App.msg_invalid_request_status'));
        }

        $request = $this->tripRequestModel
            ->select('trip_requests.*, trips.departure_date, trips.arrival_date, ships.capacity AS ship_capacity')
            ->join('trips', 'trips.id = trip_requests.trip_id')
            ->join('ships', 'ships.id = trips.ship_id')
            ->find($id);

        if (! $request) {
            return redirect()->to(site_url('trip-requests'))->with('error', lang('App.msg_trip_request_not_found'));
        }
        if (($request['status'] ?? '') !== 'pending') {
            return redirect()->to(site_url('trip-requests'))->with('error', lang('App.msg_request_already_processed'));
        }
        if ($status === 'approved' && strtotime((string) $request['departure_date']) <= time()) {
            return redirect()->to(site_url('trip-requests'))->with('error', lang('App.msg_cannot_approve_departed_trip'));
        }
        if ($status === 'approved' && $this->userHasOverlappingApprovedTrip((int) ($request['user_id'] ?? 0), $request)) {
            return redirect()->to(site_url('trip-requests'))->with('error', lang('App.msg_user_has_overlapping_approved_trip_admin'));
        }
        if ($status === 'approved' && ! $this->canFitRequest((int) ($request['trip_id'] ?? 0), $this->requestPeopleCount($request))) {
            return redirect()->to(site_url('trip-requests'))->with('error', lang('App.msg_trip_full_cannot_approve_people'));
        }

        $db = db_connect();
        $db->transBegin();

        try {
            if ($status === 'approved' && $this->userHasOverlappingApprovedTrip((int) ($request['user_id'] ?? 0), $request)) {
                throw new \RuntimeException('User has an overlapping approved trip.');
            }
            if ($status === 'approved' && ! $this->canFitRequest((int) ($request['trip_id'] ?? 0), $this->requestPeopleCount($request))) {
                throw new \RuntimeException('Trip capacity cannot fit this request.');
            }

            $updated = $db->table('trip_requests')
                ->where('id', $id)
                ->where('status', 'pending')
                ->update(['status' => $status, 'updated_at' => date('Y-m-d H:i:s')]);
            if (! $updated || $db->affectedRows() !== 1) {
                throw new \RuntimeException('Trip request was already processed by another operation.');
            }

            if (! $this->notificationModel->insert([
                'user_id' => (int) $request['user_id'],
                'type' => 'request_status',
                'title' => lang('App.notification_request_' . $status . '_title'),
                'message' => lang('App.notification_request_' . $status . '_message'),
                'related_type' => 'trip_request',
                'related_id' => $id,
                'is_read' => 0,
            ])) {
                throw new \RuntimeException(implode(' | ', $this->notificationModel->errors()));
            }

            $db->transCommit();
        } catch (\Throwable $e) {
            $db->transRollback();
            log_message('error', 'Trip request status update failed: {message}', ['message' => $e->getMessage()]);
            return redirect()->to(site_url('trip-requests'))->with('error', lang('App.msg_operation_failed'));
        }

        (new ActivityLogger())->log('trip_request.' . $status, sprintf(lang('App.activity_trip_request_status'), $id, request_status_label($status)), 'trip_request', $id);
        return redirect()->to(site_url('trip-requests'))->with('success', lang('App.msg_trip_request_updated'));
    }

    private function requestPeopleCount(array $request): int
    {
        return max(1, (int) ($request['people_count'] ?? 1));
    }

    private function canFitRequest(int $tripId, int $peopleCount): bool
    {
        if ($tripId < 1 || $peopleCount < 1) {
            return false;
        }

        $status = $this->tripCapacityStatus($tripId);
        return $status['capacity'] <= 0 || $peopleCount <= $status['remaining'];
    }

    private function tripCapacityStatus(int $tripId): array
    {
        $db = db_connect();
        $row = $db->table('trips')
            ->select('ships.capacity AS capacity')
            ->join('ships', 'ships.id = trips.ship_id')
            ->where('trips.id', $tripId)
            ->get()
            ->getRowArray();

        $capacity = max(0, (int) ($row['capacity'] ?? 0));

        if ($db->fieldExists('people_count', 'trip_requests')) {
            $approvedRow = $db->table('trip_requests')
                ->select('COALESCE(SUM(COALESCE(people_count, 1)), 0) AS total', false)
                ->where('trip_id', $tripId)
                ->where('status', 'approved')
                ->get()
                ->getRowArray();
            $approved = max(0, (int) ($approvedRow['total'] ?? 0));
        } else {
            $approved = (int) (new TripRequestModel())
                ->where('trip_id', $tripId)
                ->where('status', 'approved')
                ->countAllResults();
        }

        return [
            'capacity' => $capacity,
            'approved' => $approved,
            'remaining' => max(0, $capacity - $approved),
        ];
    }

    private function userHasOverlappingApprovedTrip(int $userId, array $trip): bool
    {
        if ($userId < 1 || empty($trip['departure_date']) || empty($trip['arrival_date'])) {
            return false;
        }

        return (new TripRequestModel())
            ->join('trips', 'trips.id = trip_requests.trip_id')
            ->where('trip_requests.user_id', $userId)
            ->where('trip_requests.status', 'approved')
            ->where('trips.id !=', (int) ($trip['trip_id'] ?? $trip['id'] ?? 0))
            ->where('trips.departure_date <', (string) $trip['arrival_date'])
            ->where('trips.arrival_date >', (string) $trip['departure_date'])
            ->countAllResults() > 0;
    }

    private function notifyAdmins(string $title, string $message, int $requestId): void
    {
        try {
            $admins = (new User())->select('id')->where('role', 'admin')->where('status', 'active')->findAll();
            foreach ($admins as $admin) {
                if (! $this->notificationModel->insert([
                    'user_id' => (int) $admin['id'],
                    'type' => 'trip_request',
                    'title' => $title,
                    'message' => $message,
                    'related_type' => 'trip_request',
                    'related_id' => $requestId,
                    'is_read' => 0,
                ])) {
                    log_message('warning', 'Admin notification could not be created for trip request {id}.', ['id' => $requestId]);
                }
            }
        } catch (\Throwable $e) {
            log_message('error', 'Trip request notifications unavailable: {message}', ['message' => $e->getMessage()]);
        }
    }
}
