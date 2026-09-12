<?php

namespace App\Libraries;

use CodeIgniter\Database\BaseConnection;

class PortPlaceSynchronizer
{
    private BaseConnection $db;

    public function __construct(?BaseConnection $db = null)
    {
        $this->db = $db ?? db_connect();
    }

    /**
     * Create missing ports from active catalogue places and link matching legacy ports.
     *
     * @return array{created:int,linked:int,skipped:int,total:int}
     */
    public function sync(): array
    {
        if (! $this->db->tableExists('places') || ! $this->db->tableExists('ports')) {
            return ['created' => 0, 'linked' => 0, 'skipped' => 0, 'total' => 0];
        }

        $places = $this->db->table('places')
            ->select('id, code, name, name_en, country, country_en, city_en, latitude, longitude, status')
            ->where('status', 'active')
            ->orderBy('country_en', 'ASC')
            ->orderBy('name_en', 'ASC')
            ->get()
            ->getResultArray();

        $stats = ['created' => 0, 'linked' => 0, 'skipped' => 0, 'total' => count($places)];
        if ($places === []) {
            return $stats;
        }

        $hasPlaceLink = $this->db->fieldExists('place_id', 'ports');
        $now = date('Y-m-d H:i:s');

        foreach ($places as $place) {
            $existing = $this->findExistingPortForPlace($place, $hasPlaceLink);
            $placeId = (int) ($place['id'] ?? 0);

            if ($existing !== null) {
                if ($hasPlaceLink && $placeId > 0 && empty($existing['place_id'])) {
                    $this->db->table('ports')
                        ->where('id', (int) $existing['id'])
                        ->update(['place_id' => $placeId, 'updated_at' => $now]);
                    $stats['linked']++;
                } else {
                    $stats['skipped']++;
                }
                continue;
            }

            $payload = $this->portPayloadFromPlace($place);
            if ($payload['name'] === '' || $payload['country'] === '' || $payload['latitude'] === '' || $payload['longitude'] === '') {
                $stats['skipped']++;
                continue;
            }

            if ($hasPlaceLink) {
                $payload['place_id'] = $placeId;
            }
            $payload['created_at'] = $now;
            $payload['updated_at'] = $now;

            $this->db->table('ports')->insert($payload);
            $stats['created']++;
        }

        return $stats;
    }

    private function findExistingPortForPlace(array $place, bool $hasPlaceLink): ?array
    {
        $placeId = (int) ($place['id'] ?? 0);

        if ($hasPlaceLink && $placeId > 0) {
            $linked = $this->db->table('ports')->where('place_id', $placeId)->get()->getRowArray();
            if ($linked) {
                return $linked;
            }
        }

        $payload = $this->portPayloadFromPlace($place);
        $builder = $this->db->table('ports')->select('id, place_id, name, country, city, latitude, longitude');
        $builder->groupStart()
            ->where('name', $payload['name'])
            ->where('country', $payload['country'])
        ->groupEnd()
        ->orGroupStart()
            ->where('latitude', $payload['latitude'])
            ->where('longitude', $payload['longitude'])
        ->groupEnd();

        $row = $builder->get()->getRowArray();
        return $row ?: null;
    }

    private function portPayloadFromPlace(array $place): array
    {
        $country = trim((string) ($place['country_en'] ?? '')) ?: trim((string) ($place['country'] ?? ''));

        return [
            'name' => trim((string) ($place['name_en'] ?? '')) ?: trim((string) ($place['name'] ?? '')),
            'country' => $country,
            'city' => trim((string) ($place['city_en'] ?? '')) ?: $country,
            'latitude' => trim((string) ($place['latitude'] ?? '')),
            'longitude' => trim((string) ($place['longitude'] ?? '')),
        ];
    }
}
