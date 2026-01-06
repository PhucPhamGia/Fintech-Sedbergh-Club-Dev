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

        $this->forge->addColumn('auth', $fields);

        // Add unique index for selector lookups.
        // MySQL/MariaDB syntax.
        $this->db->query('CREATE UNIQUE INDEX auth_remember_selector_uq ON auth (remember_selector)');
    }

    public function down()
    {
        // Drop unique index first (MySQL/MariaDB syntax).
        try {
            $this->db->query('DROP INDEX auth_remember_selector_uq ON auth');
        } catch (\Throwable $e) {
            // Ignore if it does not exist.
        }

        $this->forge->dropColumn('auth', ['remember_selector', 'remember_hash', 'remember_expires_at']);
    }
}
