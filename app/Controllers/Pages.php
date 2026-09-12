<?php

namespace App\Controllers;

use App\Models\Place;
use App\Models\Port;
use App\Models\Ship;
use App\Models\Trip;

class Pages extends BaseController
{
    public function about() { return view('pages/about'); }
    public function services() { return view('pages/services'); }

    public function portsDirectory()
    {
        $q = trim((string) $this->request->getGet('q'));
        $country = trim((string) $this->request->getGet('country'));

        if ($this->canUsePlacesDirectory()) {
            return $this->realPortsDirectory($q, $country);
        }

        return $this->legacyPortsDirectory($q, $country);
    }

    private function realPortsDirectory(string $q, string $country)
    {
        $model = new Place();
        $builder = $model->where('type', 'port')->where('status', 'active');

        if ($q !== '') {
            $builder->groupStart()
                ->like('name', $q)
                ->orLike('name_en', $q)
                ->orLike('name_ar', $q)
                ->orLike('city_en', $q)
                ->orLike('city_ar', $q)
                ->orLike('country', $q)
                ->orLike('country_en', $q)
                ->orLike('country_ar', $q)
                ->orLike('code', $q)
                ->groupEnd();
        }

        if ($country !== '') {
            $builder->groupStart()
                ->where('country_code', strtoupper($country))
                ->orWhere('country', $country)
                ->orWhere('country_en', $country)
                ->orWhere('country_ar', $country)
                ->groupEnd();
        }

        $countries = $this->placeCountries();
        $ports = $builder
            ->orderBy('country_en', 'ASC')
            ->orderBy('name_en', 'ASC')
            ->paginate(12);

        return view('pages/ports', [
            'ports' => $ports,
            'pager' => $model->pager,
            'countries' => $countries,
            'filters' => ['q' => $q, 'country' => $country],
            'usesPlacesDirectory' => true,
            'directoryTotal' => count($ports),
        ]);
    }

    private function legacyPortsDirectory(string $q, string $country)
    {
        $model = new Port();
        $builder = $model;

        if ($q !== '') {
            $builder->groupStart()->like('name', $q)->orLike('city', $q)->orLike('country', $q)->groupEnd();
        }
        if ($country !== '') {
            $builder->where('country', $country);
        }

        $countries = (new Port())->select('country')->groupBy('country')->orderBy('country')->findAll();
        $ports = $builder->orderBy('name')->paginate(12);

        return view('pages/ports', [
            'ports' => $ports,
            'pager' => $model->pager,
            'countries' => array_map(static fn ($row) => [
                'value' => (string) ($row['country'] ?? ''),
                'label' => (string) ($row['country'] ?? ''),
            ], $countries),
            'filters' => ['q' => $q, 'country' => $country],
            'usesPlacesDirectory' => false,
            'directoryTotal' => count($ports),
        ]);
    }

    private function canUsePlacesDirectory(): bool
    {
        try {
            $db = db_connect();

            return $db->tableExists('places')
                && $db->fieldExists('type', 'places')
                && $db->fieldExists('status', 'places')
                && $db->fieldExists('country_code', 'places');
        } catch (\Throwable $e) {
            return false;
        }
    }

    private function placeCountries(): array
    {
        $locale = (string) service('request')->getLocale();
        $rows = (new Place())
            ->select('country_code, country, country_en, country_ar')
            ->where('type', 'port')
            ->where('status', 'active')
            ->groupBy('country_code, country, country_en, country_ar')
            ->orderBy($locale === 'ar' ? 'country_ar' : 'country_en', 'ASC')
            ->findAll();

        $countries = [];
        foreach ($rows as $row) {
            $value = trim((string) ($row['country_code'] ?? ''));
            if ($value === '') {
                $value = (string) ($row['country_en'] ?? $row['country'] ?? '');
            }
            $label = $locale === 'ar'
                ? (string) ($row['country_ar'] ?? $row['country'] ?? $row['country_en'] ?? $value)
                : (string) ($row['country_en'] ?? $row['country'] ?? $row['country_ar'] ?? $value);

            if ($value !== '' && $label !== '') {
                $countries[$value] = ['value' => $value, 'label' => $label];
            }
        }

        return array_values($countries);
    }

    public function shipsDirectory()
    {
        $q = trim((string) $this->request->getGet('q'));
        $type = trim((string) $this->request->getGet('type'));
        $model = new Ship();
        $builder = $model;

        if ($q !== '') {
            $builder->groupStart()->like('name', $q)->orLike('type', $q)->groupEnd();
        }
        if ($type !== '') {
            $builder->where('type', $type);
        }

        $types = (new Ship())->select('type')->groupBy('type')->orderBy('type')->findAll();
        $ships = $builder->orderBy('name')->paginate(12);
        $shipTrips = $this->shipScheduleMap(array_map(static fn ($ship) => (int) ($ship['id'] ?? 0), $ships));

        return view('pages/ships', [
            'ships' => $ships,
            'pager' => $model->pager,
            'types' => array_column($types, 'type'),
            'filters' => ['q' => $q, 'type' => $type],
            'shipTrips' => $shipTrips,
            'directoryTotal' => count($ships),
        ]);
    }

    private function shipScheduleMap(array $shipIds): array
    {
        $shipIds = array_values(array_filter(array_unique($shipIds)));
        if ($shipIds === []) {
            return [];
        }

        try {
            $db = db_connect();
            if (! $db->tableExists('trips')) {
                return [];
            }

            $rows = (new Trip())
                ->select('trips.*, departure.name AS departure_name, arrival.name AS arrival_name')
                ->join('ports AS departure', 'departure.id = trips.departure_port_id', 'left')
                ->join('ports AS arrival', 'arrival.id = trips.arrival_port_id', 'left')
                ->whereIn('trips.ship_id', $shipIds)
                ->where('trips.arrival_date >=', date('Y-m-d H:i:s'))
                ->orderBy('trips.departure_date', 'ASC')
                ->findAll();
        } catch (\Throwable $e) {
            return [];
        }

        $now = time();
        $map = [];
        foreach ($rows as $row) {
            $shipId = (int) ($row['ship_id'] ?? 0);
            if ($shipId <= 0 || isset($map[$shipId])) {
                continue;
            }

            $departureAt = strtotime((string) ($row['departure_date'] ?? '')) ?: 0;
            $arrivalAt = strtotime((string) ($row['arrival_date'] ?? '')) ?: 0;
            $map[$shipId] = [
                'status' => $departureAt <= $now && $arrivalAt >= $now ? 'busy' : 'scheduled',
                'departure_name' => (string) ($row['departure_name'] ?? ''),
                'arrival_name' => (string) ($row['arrival_name'] ?? ''),
                'departure_date' => (string) ($row['departure_date'] ?? ''),
                'arrival_date' => (string) ($row['arrival_date'] ?? ''),
            ];
        }

        return $map;
    }

    public function news() { return view('pages/news'); }
    public function privacy() { return view('pages/privacy'); }
    public function terms() { return view('pages/terms'); }
    public function faq() { return view('pages/faq'); }
}
