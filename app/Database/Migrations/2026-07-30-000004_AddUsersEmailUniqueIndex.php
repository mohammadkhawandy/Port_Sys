<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddUsersEmailUniqueIndex extends Migration
{
    private const INDEX_NAME = 'users_email_unique';

    public function up()
    {
        $indexes = $this->db->getIndexData('users');
        if (isset($indexes[self::INDEX_NAME])) {
            return;
        }

        $duplicate = $this->db->table('users')
            ->select('email, COUNT(*) AS duplicate_count')
            ->groupBy('email')
            ->having('COUNT(*) >', 1, false)
            ->get(1)
            ->getRowArray();

        if ($duplicate) {
            throw new \RuntimeException('Cannot add users.email unique index while duplicate email values exist.');
        }

        $this->db->query('CREATE UNIQUE INDEX ' . self::INDEX_NAME . ' ON users (email)');
    }

    public function down()
    {
        $driver = strtolower((string) $this->db->DBDriver);
        if (str_contains($driver, 'mysql')) {
            $this->db->query('DROP INDEX ' . self::INDEX_NAME . ' ON users');
            return;
        }

        $this->db->query('DROP INDEX ' . self::INDEX_NAME);
    }
}
