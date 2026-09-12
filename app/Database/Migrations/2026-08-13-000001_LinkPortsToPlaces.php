<?php

namespace App\Database\Migrations;

use App\Libraries\PortPlaceSynchronizer;
use CodeIgniter\Database\Migration;

class LinkPortsToPlaces extends Migration
{
    public function up()
    {
        if (! $this->db->tableExists('ports')) {
            return;
        }

        if (! $this->db->fieldExists('place_id', 'ports')) {
            $this->forge->addColumn('ports', [
                'place_id' => [
                    'type' => 'INT',
                    'constraint' => 11,
                    'unsigned' => true,
                    'null' => true,
                    'after' => 'id',
                ],
            ]);
        }

        $this->createPlaceIndexIfMissing();
        $this->backfillPlaceLinks();
        (new PortPlaceSynchronizer($this->db))->sync();
    }

    public function down()
    {
        if ($this->db->tableExists('ports') && $this->db->fieldExists('place_id', 'ports')) {
            $this->forge->dropColumn('ports', 'place_id');
        }
    }

    private function createPlaceIndexIfMissing(): void
    {
        if (! $this->db->fieldExists('place_id', 'ports')) {
            return;
        }

        try {
            $indexes = $this->db->getIndexData('ports');
            foreach ($indexes as $index) {
                if (($index->name ?? '') === 'idx_ports_place_id') {
                    return;
                }
            }
        } catch (\Throwable $exception) {
            // Older database drivers may not expose index metadata consistently.
        }

        try {
            $prefix = $this->db->getPrefix();
            $this->db->query('CREATE INDEX `idx_ports_place_id` ON `' . $prefix . 'ports` (`place_id`)');
        } catch (\Throwable $exception) {
            // Index creation is an optimization only. Never block deployment on it.
        }
    }

    private function backfillPlaceLinks(): void
    {
        if (! $this->db->tableExists('places') || ! $this->db->fieldExists('place_id', 'ports')) {
            return;
        }

        $ports = $this->db->table('ports')
            ->select('id, name, country, city, latitude, longitude, place_id')
            ->groupStart()
                ->where('place_id', null)
                ->orWhere('place_id', 0)
            ->groupEnd()
            ->get()
            ->getResultArray();

        if ($ports === []) {
            return;
        }

        $places = $this->db->table('places')
            ->select('id, name, name_en, country, country_en, city_en, latitude, longitude')
            ->get()
            ->getResultArray();

        foreach ($ports as $port) {
            $matchId = $this->findMatchingPlaceId($port, $places);
            if ($matchId > 0) {
                $this->db->table('ports')->where('id', (int) $port['id'])->update([
                    'place_id' => $matchId,
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);
            }
        }
    }

    private function findMatchingPlaceId(array $port, array $places): int
    {
        $portName = mb_strtolower(trim((string) ($port['name'] ?? '')));
        $portCountry = mb_strtolower(trim((string) ($port['country'] ?? '')));
        $portCity = mb_strtolower(trim((string) ($port['city'] ?? '')));
        $portLat = (float) ($port['latitude'] ?? 0);
        $portLng = (float) ($port['longitude'] ?? 0);

        foreach ($places as $place) {
            $placeNames = [
                mb_strtolower(trim((string) ($place['name'] ?? ''))),
                mb_strtolower(trim((string) ($place['name_en'] ?? ''))),
            ];
            $placeCountries = [
                mb_strtolower(trim((string) ($place['country'] ?? ''))),
                mb_strtolower(trim((string) ($place['country_en'] ?? ''))),
            ];
            $placeCity = mb_strtolower(trim((string) ($place['city_en'] ?? '')));
            $sameCoordinates = abs($portLat - (float) ($place['latitude'] ?? 0)) < 0.00001
                && abs($portLng - (float) ($place['longitude'] ?? 0)) < 0.00001;
            $sameName = in_array($portName, array_filter($placeNames), true);
            $sameCountry = in_array($portCountry, array_filter($placeCountries), true);
            $sameCity = $portCity === '' || $placeCity === '' || $portCity === $placeCity;

            if ($sameCoordinates || ($sameName && $sameCountry && $sameCity)) {
                return (int) ($place['id'] ?? 0);
            }
        }

        return 0;
    }
}
