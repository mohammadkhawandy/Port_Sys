<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run()
    {
        $users = [
            [
                'full_name' => 'System Administrator',
                'email' => (string) env('PORTSYS_ADMIN_EMAIL', 'admin@example.com'),
                'password' => (string) env('PORTSYS_ADMIN_PASSWORD', 'PortSys@2026'),
                'role' => 'admin',
                'status' => 'active',
            ],
            [
                'full_name' => 'Demo User',
                'email' => (string) env('PORTSYS_USER_EMAIL', 'user@example.com'),
                'password' => (string) env('PORTSYS_USER_PASSWORD', 'User@2026'),
                'role' => 'user',
                'status' => 'active',
            ],
        ];

        $fields = $this->db->getFieldNames('users');

        foreach ($users as $user) {
            $email = mb_strtolower(trim($user['email']));
            $existing = $this->db->table('users')->select('id')->where('email', $email)->get()->getRowArray();

            // Seeders must never reset a real account password when they are run again.
            if ($existing) {
                continue;
            }

            $data = [
                'email' => $email,
                'password' => password_hash($user['password'], PASSWORD_DEFAULT),
                'role' => $user['role'],
            ];
            foreach (['full_name', 'status'] as $optional) {
                if (in_array($optional, $fields, true)) {
                    $data[$optional] = $user[$optional];
                }
            }

            $this->db->table('users')->insert($data);
        }
    }
}
