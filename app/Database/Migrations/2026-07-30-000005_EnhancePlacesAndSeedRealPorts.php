<?php

namespace App\Database\Migrations;

use App\Libraries\RealPortCatalog;
use CodeIgniter\Database\Migration;

class EnhancePlacesAndSeedRealPorts extends Migration
{
    public function up()
    {
        if (! $this->db->tableExists('places')) {
            return;
        }

        $columns = [
            'code' => ['type' => 'VARCHAR', 'constraint' => 12, 'null' => true, 'after' => 'id'],
            'name_en' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true, 'after' => 'name'],
            'name_ar' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true, 'after' => 'name_en'],
            'country_code' => ['type' => 'CHAR', 'constraint' => 2, 'null' => true, 'after' => 'country'],
            'country_en' => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true, 'after' => 'country_code'],
            'country_ar' => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true, 'after' => 'country_en'],
            'city_en' => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true, 'after' => 'country_ar'],
            'city_ar' => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true, 'after' => 'city_en'],
            'type' => ['type' => 'VARCHAR', 'constraint' => 30, 'default' => 'port', 'after' => 'longitude'],
            'timezone' => ['type' => 'VARCHAR', 'constraint' => 64, 'null' => true, 'after' => 'type'],
            'status' => ['type' => 'VARCHAR', 'constraint' => 20, 'default' => 'active', 'after' => 'timezone'],
            'is_seeded' => ['type' => 'TINYINT', 'constraint' => 1, 'unsigned' => true, 'default' => 0, 'after' => 'status'],
        ];

        foreach ($columns as $name => $definition) {
            if (! $this->db->fieldExists($name, 'places')) {
                $this->forge->addColumn('places', [$name => $definition]);
            }
        }

        // Preserve legacy records created before bilingual/location metadata
        // was introduced, so existing installations remain fully searchable.
        $legacyBuilder = $this->db->table('places');
        $legacyRows = $legacyBuilder
            ->select('id, name, name_en, country, country_en, type, status')
            ->get()
            ->getResultArray();
        foreach ($legacyRows as $legacy) {
            $updates = [];
            if (trim((string) ($legacy['name_en'] ?? '')) === '') {
                $updates['name_en'] = (string) ($legacy['name'] ?? '');
            }
            if (trim((string) ($legacy['country_en'] ?? '')) === '') {
                $updates['country_en'] = (string) ($legacy['country'] ?? '');
            }
            if (trim((string) ($legacy['type'] ?? '')) === '') {
                $updates['type'] = 'port';
            }
            if (trim((string) ($legacy['status'] ?? '')) === '') {
                $updates['status'] = 'active';
            }
            if ($updates !== []) {
                $legacyBuilder->where('id', (int) $legacy['id'])->update($updates);
            }
        }

        $builder = $this->db->table('places');
        foreach (RealPortCatalog::all() as $place) {
            $exists = $builder->where('code', $place['code'])->countAllResults() > 0;
            if (! $exists) {
                $builder->insert(array_merge($place, [
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ]));
            }
        }
    }

    public function down()
    {
        if (! $this->db->tableExists('places')) {
            return;
        }

        if ($this->db->fieldExists('is_seeded', 'places')) {
            $this->db->table('places')->where('is_seeded', 1)->delete();
        }

        foreach ([
            'code', 'name_en', 'name_ar', 'country_code', 'country_en', 'country_ar',
            'city_en', 'city_ar', 'type', 'timezone', 'status', 'is_seeded',
        ] as $column) {
            if ($this->db->fieldExists($column, 'places')) {
                $this->forge->dropColumn('places', $column);
            }
        }
    }
}
