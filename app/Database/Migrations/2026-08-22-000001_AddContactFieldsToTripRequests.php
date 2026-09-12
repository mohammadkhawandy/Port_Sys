<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddContactFieldsToTripRequests extends Migration
{
    public function up()
    {
        $fields = [
            'contact_name' => [
                'type' => 'VARCHAR',
                'constraint' => 150,
                'null' => true,
                'after' => 'request_type',
            ],
            'contact_phone' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => true,
                'after' => 'contact_name',
            ],
            'contact_email' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
                'after' => 'contact_phone',
            ],
            'organization' => [
                'type' => 'VARCHAR',
                'constraint' => 150,
                'null' => true,
                'after' => 'contact_email',
            ],
        ];

        foreach ($fields as $name => $definition) {
            if (! $this->db->fieldExists($name, 'trip_requests')) {
                $this->forge->addColumn('trip_requests', [$name => $definition]);
            }
        }
    }

    public function down()
    {
        foreach (['organization', 'contact_email', 'contact_phone', 'contact_name'] as $name) {
            if ($this->db->fieldExists($name, 'trip_requests')) {
                $this->forge->dropColumn('trip_requests', $name);
            }
        }
    }
}
