<?php

namespace App\Controllers;

use App\Libraries\ActivityLogger;
use App\Libraries\PortPlaceSynchronizer;
use App\Models\Place as PlaceModel;
use App\Models\Port as PortModel;
use App\Models\Trip;
use CodeIgniter\Exceptions\PageNotFoundException;

class Port extends BaseController
{
    protected PortModel $portModel;

    public function __construct()
    {
        $this->portModel = new PortModel();
    }

    public function index()
    {
        $q = trim((string) $this->request->getGet('q'));
        $country = trim((string) $this->request->getGet('country'));
        $builder = $this->portModel;

        if ($q !== '') {
            $builder->groupStart()->like('name', $q)->orLike('city', $q)->orLike('country', $q)->groupEnd();
        }
        if ($country !== '') {
            $builder->where('country', $country);
        }

        $filteredCountBuilder = new PortModel();
        if ($q !== '') {
            $filteredCountBuilder->groupStart()->like('name', $q)->orLike('city', $q)->orLike('country', $q)->groupEnd();
        }
        if ($country !== '') {
            $filteredCountBuilder->where('country', $country);
        }
        $filteredCount = $filteredCountBuilder->countAllResults();

        $ports = $builder->orderBy('name', 'ASC')->paginate(18);
        $countries = (new PortModel())->select('country')->groupBy('country')->orderBy('country', 'ASC')->findAll();

        return view('ports/index', [
            'ports' => $ports,
            'pager' => $this->portModel->pager,
            'filters' => ['q' => $q, 'country' => $country],
            'countries' => array_column($countries, 'country'),
            'availablePlaces' => $this->availablePlacesForImport(),
            'hasPlaceLink' => $this->portsHavePlaceLink(),
            'stats' => [
                'total_ports' => (new PortModel())->countAllResults(),
                'filtered_ports' => $filteredCount,
                'countries_count' => count($countries),
            ],
        ]);
    }

    public function create()
    {
        return view('ports/create', [
            'places' => $this->activePlaces(),
            'hasPlaceLink' => $this->portsHavePlaceLink(),
        ]);
    }

    public function store()
    {
        $data = $this->payload();
        if (! $this->portModel->insert($data)) {
            return redirect()->back()->withInput()->with('errors', $this->portModel->errors());
        }

        $id = (int) $this->portModel->getInsertID();
        (new ActivityLogger())->log('port.created', sprintf(lang('App.activity_port_created'), $data['name']), 'port', $id);
        return redirect()->to(site_url('ports'))->with('success', lang('App.msg_port_created'));
    }

    public function importPlace()
    {
        $placeId = (int) $this->request->getPost('place_id');
        if ($placeId <= 0) {
            return redirect()->back()->with('error', lang('App.msg_place_required'));
        }

        $place = (new PlaceModel())->where('status', 'active')->find($placeId);
        if (! $place) {
            return redirect()->back()->with('error', lang('App.msg_place_not_found'));
        }

        $existingPort = $this->findExistingPortForPlace($place);
        if ($existingPort !== null) {
            return redirect()
                ->to(site_url('ports/edit/' . (int) $existingPort['id']))
                ->with('error', lang('App.msg_port_already_linked_to_place'));
        }

        $data = $this->portPayloadFromPlace($place);
        if ($this->portsHavePlaceLink()) {
            $data['place_id'] = $placeId;
        }

        if (! $this->portModel->insert($data)) {
            return redirect()->back()->with('errors', $this->portModel->errors());
        }

        $id = (int) $this->portModel->getInsertID();
        (new ActivityLogger())->log('port.created', sprintf(lang('App.activity_port_created'), $data['name']), 'port', $id);

        return redirect()->to(site_url('ports'))->with('success', sprintf(lang('App.msg_port_imported_from_place'), $data['name']));
    }

    public function syncFromPlaces()
    {
        $stats = (new PortPlaceSynchronizer())->sync();

        if (($stats['created'] ?? 0) > 0 || ($stats['linked'] ?? 0) > 0) {
            (new ActivityLogger())->log(
                'port.synced_places',
                sprintf(lang('App.activity_ports_synced_from_places'), (int) $stats['created'], (int) $stats['linked']),
                'port'
            );
        }

        return redirect()->to(site_url('ports'))->with(
            'success',
            sprintf(
                lang('App.msg_ports_synced_from_places'),
                (int) $stats['created'],
                (int) $stats['linked'],
                (int) $stats['skipped']
            )
        );
    }

    public function edit(int $id)
    {
        $port = $this->portModel->find($id);
        if (! $port) {
            throw PageNotFoundException::forPageNotFound(lang('App.msg_port_not_found'));
        }

        return view('ports/edit', [
            'port' => $port,
            'places' => $this->activePlaces(),
            'hasPlaceLink' => $this->portsHavePlaceLink(),
        ]);
    }

    public function update(int $id)
    {
        $port = $this->portModel->find($id);
        if (! $port) {
            return redirect()->to(site_url('ports'))->with('error', lang('App.msg_port_not_found'));
        }

        $data = $this->payload();
        if (! $this->portModel->update($id, $data)) {
            return redirect()->back()->withInput()->with('errors', $this->portModel->errors());
        }

        (new ActivityLogger())->log('port.updated', sprintf(lang('App.activity_port_updated'), $data['name']), 'port', $id);
        return redirect()->to(site_url('ports'))->with('success', lang('App.msg_port_updated'));
    }

    public function delete(int $id)
    {
        $port = $this->portModel->find($id);
        if (! $port) {
            return redirect()->to(site_url('ports'))->with('error', lang('App.msg_port_not_found'));
        }

        $tripCount = (new Trip())
            ->groupStart()->where('departure_port_id', $id)->orWhere('arrival_port_id', $id)->groupEnd()
            ->countAllResults();
        if ($tripCount > 0) {
            return redirect()->to(site_url('ports'))->with('error', lang('App.msg_port_in_use'));
        }

        if (! $this->portModel->delete($id)) {
            return redirect()->back()->with('error', lang('App.msg_port_delete_failed'));
        }
        (new ActivityLogger())->log('port.deleted', sprintf(lang('App.activity_port_deleted'), $port['name']), 'port', $id);
        return redirect()->to(site_url('ports'))->with('success', lang('App.msg_port_deleted'));
    }

    private function payload(): array
    {
        $placeId = (int) $this->request->getPost('place_id');
        $place = $placeId > 0 ? (new PlaceModel())->where('status', 'active')->find($placeId) : null;

        $data = [
            'name' => trim((string) $this->request->getPost('name')),
            'country' => trim((string) $this->request->getPost('country')),
            'city' => trim((string) $this->request->getPost('city')),
            'latitude' => trim((string) $this->request->getPost('latitude')),
            'longitude' => trim((string) $this->request->getPost('longitude')),
        ];

        // If JavaScript is disabled and the user selected a catalogue location,
        // keep the form usable by filling any missing field on the server side.
        if ($place) {
            $fromPlace = $this->portPayloadFromPlace($place);
            foreach ($data as $key => $value) {
                if ($value === '') {
                    $data[$key] = $fromPlace[$key] ?? $value;
                }
            }

            if ($this->portsHavePlaceLink()) {
                $data['place_id'] = $placeId;
            }
        } elseif ($this->portsHavePlaceLink()) {
            $data['place_id'] = null;
        }

        return $data;
    }

    private function activePlaces(): array
    {
        return (new PlaceModel())
            ->where('status', 'active')
            ->orderBy('country_en', 'ASC')
            ->orderBy('name_en', 'ASC')
            ->findAll();
    }

    private function availablePlacesForImport(): array
    {
        $placeModel = new PlaceModel();
        $placeModel->where('status', 'active')
            ->orderBy('country_en', 'ASC')
            ->orderBy('name_en', 'ASC');

        if ($this->portsHavePlaceLink()) {
            $linkedRows = (new PortModel())
                ->select('place_id')
                ->where('place_id IS NOT NULL', null, false)
                ->where('place_id >', 0)
                ->findAll();
            $linkedIds = array_values(array_filter(array_map(static fn ($row) => (int) ($row['place_id'] ?? 0), $linkedRows)));
            if ($linkedIds !== []) {
                $placeModel->whereNotIn('id', $linkedIds);
            }
        }

        return $placeModel->findAll();
    }

    private function findExistingPortForPlace(array $place): ?array
    {
        $placeId = (int) ($place['id'] ?? 0);
        if ($this->portsHavePlaceLink() && $placeId > 0) {
            $linked = (new PortModel())->where('place_id', $placeId)->first();
            if ($linked) {
                return $linked;
            }
        }

        $payload = $this->portPayloadFromPlace($place);
        $same = (new PortModel())
            ->groupStart()
                ->where('name', $payload['name'])
                ->where('country', $payload['country'])
            ->groupEnd()
            ->orGroupStart()
                ->where('latitude', $payload['latitude'])
                ->where('longitude', $payload['longitude'])
            ->groupEnd()
            ->first();

        return $same ?: null;
    }

    private function portPayloadFromPlace(array $place): array
    {
        return [
            'name' => trim((string) ($place['name_en'] ?? $place['name'] ?? '')),
            'country' => trim((string) ($place['country_en'] ?? $place['country'] ?? '')),
            'city' => trim((string) ($place['city_en'] ?? '')) ?: trim((string) ($place['country_en'] ?? $place['country'] ?? '')),
            'latitude' => trim((string) ($place['latitude'] ?? '')),
            'longitude' => trim((string) ($place['longitude'] ?? '')),
        ];
    }

    private function portsHavePlaceLink(): bool
    {
        static $hasColumn = null;
        if ($hasColumn === null) {
            $hasColumn = db_connect()->fieldExists('place_id', 'ports');
        }

        return $hasColumn;
    }
}
