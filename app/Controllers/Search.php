<?php

namespace App\Controllers;

use App\Models\Place;
use App\Models\Port;
use App\Models\Ship;
use App\Models\Trip;

class Search extends BaseController
{
    public function index()
    {
        $query = trim((string) $this->request->getGet('q'));
        if (mb_strlen($query) < 2) {
            return $this->response->setJSON(['items' => []]);
        }

        $items = [];


        if (is_admin_user()) {
            $places = (new Place())
                ->groupStart()
                    ->like('name', $query)
                    ->orLike('name_en', $query)
                    ->orLike('name_ar', $query)
                    ->orLike('country_en', $query)
                    ->orLike('country_ar', $query)
                    ->orLike('city_en', $query)
                    ->orLike('city_ar', $query)
                    ->orLike('code', $query)
                ->groupEnd()
                ->orderBy('name_en', 'ASC')
                ->findAll(5);
            foreach ($places as $place) {
                $items[] = [
                    'type' => 'place',
                    'label' => place_display_name($place),
                    'meta' => trim(($place['code'] ?? '') . ' · ' . place_display_country($place), ' ·'),
                    'url' => site_url('places/edit/' . (int) $place['id']),
                    'icon' => 'fa-location-dot',
                ];
            }
        }

        if (can('view_ports')) {
            $ports = (new Port())
                ->groupStart()->like('name', $query)->orLike('city', $query)->orLike('country', $query)->groupEnd()
                ->orderBy('name', 'ASC')->findAll(5);
            foreach ($ports as $port) {
                $items[] = [
                    'type' => 'port',
                    'label' => $port['name'],
                    'meta' => trim(($port['city'] ?? '') . ' · ' . ($port['country'] ?? ''), ' ·'),
                    'url' => site_url('ports?q=' . rawurlencode((string) $port['name'])),
                    'icon' => 'fa-anchor',
                ];
            }
        }

        if (can('view_ships')) {
            $ships = (new Ship())
                ->groupStart()->like('name', $query)->orLike('type', $query)->groupEnd()
                ->orderBy('name', 'ASC')->findAll(5);
            foreach ($ships as $ship) {
                $items[] = [
                    'type' => 'ship',
                    'label' => $ship['name'],
                    'meta' => ($ship['type'] ?? '') . ' · ' . number_format((int) ($ship['capacity'] ?? 0)),
                    'url' => site_url('ships?q=' . rawurlencode((string) $ship['name'])),
                    'icon' => 'fa-ship',
                ];
            }
        }

        if (can('view_trips')) {
            $trips = (new Trip())
                ->select('trips.id, trips.departure_date, ships.name AS ship_name, departure_ports.name AS departure_name, arrival_ports.name AS arrival_name')
                ->join('ships', 'ships.id = trips.ship_id')
                ->join('ports AS departure_ports', 'departure_ports.id = trips.departure_port_id')
                ->join('ports AS arrival_ports', 'arrival_ports.id = trips.arrival_port_id')
                ->groupStart()
                    ->like('ships.name', $query)
                    ->orLike('departure_ports.name', $query)
                    ->orLike('arrival_ports.name', $query)
                ->groupEnd()
                ->orderBy('trips.departure_date', 'DESC')
                ->findAll(5);
            foreach ($trips as $trip) {
                $items[] = [
                    'type' => 'trip',
                    'label' => $trip['ship_name'],
                    'meta' => ($trip['departure_name'] ?? '') . ' → ' . ($trip['arrival_name'] ?? ''),
                    'url' => site_url('trips/show/' . (int) $trip['id']),
                    'icon' => 'fa-route',
                ];
            }
        }

        return $this->response
            ->setHeader('Cache-Control', 'no-store')
            ->setJSON(['items' => array_slice($items, 0, 12)]);
    }
}
