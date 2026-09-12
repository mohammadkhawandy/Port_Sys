<?php

namespace App\Database\Migrations;

use App\Libraries\PortPlaceSynchronizer;
use CodeIgniter\Database\Migration;

class AutoCreatePortsFromPlaces extends Migration
{
    public function up()
    {
        (new PortPlaceSynchronizer($this->db))->sync();
    }

    public function down()
    {
        // Intentionally empty: ports may be used by trips, so rollback must not
        // delete operational data created from the location catalogue.
    }
}
