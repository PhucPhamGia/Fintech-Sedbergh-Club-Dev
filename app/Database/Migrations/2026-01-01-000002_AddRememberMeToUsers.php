<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddRememberMeToUsers extends Migration
{
    public function up()
    {
        $fields = [
            'remember_selector' => [
                'type'       => 'VARCHAR',
                'constraint' => 32,
                'null'       => true,
            ],
            'remember_hash' => [
                'type'       => 'CHAR',
                'constraint' => 64,
                'null'       => true,
            ],
            'remember_expires_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ];

        $this->forge->addColumn('users', $fields);

        // Add unique index for selector lookups.
        // MySQL/MariaDB syntax.
        $this->db->query('CREATE UNIQUE INDEX users_remember_selector_uq ON users (remember_selector)');
    }

    public function down()
    {
        // Drop unique index first (MySQL/MariaDB syntax).
        try {
            $this->db->query('DROP INDEX users_remember_selector_uq ON users');
        } catch (\Throwable $e) {
            // Ignore if it does not exist.
        }

        $this->forge->dropColumn('users', ['remember_selector', 'remember_hash', 'remember_expires_at']);
    }
}
