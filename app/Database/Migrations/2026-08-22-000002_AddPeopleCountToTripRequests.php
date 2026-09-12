<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddPeopleCountToTripRequests extends Migration
{
    public function up()
    {
        if (! $this->db->fieldExists('people_count', 'trip_requests')) {
            $this->forge->addColumn('trip_requests', [
                'people_count' => [
                    'type' => 'INT',
                    'constraint' => 10,
                    'unsigned' => true,
                    'default' => 1,
                    'after' => 'request_type',
                ],
            ]);
        }

        $this->db->table('trip_requests')
            ->groupStart()
                ->where('people_count', null)
                ->orWhere('people_count <', 1)
            ->groupEnd()
            ->update(['people_count' => 1]);
    }

    public function down()
    {
        if ($this->db->fieldExists('people_count', 'trip_requests')) {
            $this->forge->dropColumn('trip_requests', 'people_count');
        }
    }
}
