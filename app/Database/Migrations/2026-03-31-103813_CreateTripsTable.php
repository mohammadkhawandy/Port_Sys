<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateTripsTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'constraint' => 5,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'ship_id' => [
                'type' => 'INT',
                'constraint' => 5,
                'unsigned' => true,
            ],
            'departure_port_id' => [
                'type' => 'INT',
                'constraint' => 5,
                'unsigned' => true,
            ],
            'arrival_port_id' => [
                'type' => 'INT',
                'constraint' => 5,
                'unsigned' => true,
            ],
            'departure_date' => [
                'type' => 'DATETIME',
            ],
            'arrival_date' => [
                'type' => 'DATETIME',
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey('departure_date');
        $this->forge->addKey('arrival_date');
        $this->forge->addKey(['ship_id', 'departure_date']);
        $this->forge->addForeignKey('ship_id', 'ships', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('departure_port_id', 'ports', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('arrival_port_id', 'ports', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('trips');
    }

    public function down()
    {
        $this->forge->dropTable('trips');
    }
}
