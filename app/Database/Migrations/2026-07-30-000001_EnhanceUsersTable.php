<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class EnhanceUsersTable extends Migration
{
    public function up()
    {
        $fields = $this->db->getFieldNames('users');
        $columns = [];

        if (! in_array('full_name', $fields, true)) {
            $columns['full_name'] = [
                'type' => 'VARCHAR',
                'constraint' => 150,
                'null' => true,
                'after' => 'id',
            ];
        }

        if (! in_array('status', $fields, true)) {
            $columns['status'] = [
                'type' => 'VARCHAR',
                'constraint' => 20,
                'default' => 'active',
                'after' => 'role',
            ];
        }

        if (! in_array('last_login_at', $fields, true)) {
            $columns['last_login_at'] = [
                'type' => 'DATETIME',
                'null' => true,
                'after' => 'status',
            ];
        }

        if (! in_array('updated_at', $fields, true)) {
            $columns['updated_at'] = [
                'type' => 'DATETIME',
                'null' => true,
                'after' => 'created_at',
            ];
        }

        if ($columns !== []) {
            $this->forge->addColumn('users', $columns);
        }
    }

    public function down()
    {
        $fields = $this->db->getFieldNames('users');
        foreach (['full_name', 'status', 'last_login_at', 'updated_at'] as $field) {
            if (in_array($field, $fields, true)) {
                $this->forge->dropColumn('users', $field);
            }
        }
    }
}
