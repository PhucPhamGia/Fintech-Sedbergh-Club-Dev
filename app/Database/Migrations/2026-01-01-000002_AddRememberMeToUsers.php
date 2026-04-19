<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddRememberMeToUsers extends Migration
{
    public function up()
    {
        // Guard: skip columns that already exist (safe to re-run)
        $existing = $this->db->getFieldNames('auth');

        if (!in_array('remember_selector', $existing)) {
            $this->forge->addColumn('auth', [
                'remember_selector' => ['type' => 'VARCHAR', 'constraint' => 32, 'null' => true],
                'remember_hash'     => ['type' => 'CHAR',    'constraint' => 64, 'null' => true],
                'remember_expires_at' => ['type' => 'DATETIME', 'null' => true],
            ]);
        }

        try {
            $this->db->query('CREATE UNIQUE INDEX auth_remember_selector_uq ON auth (remember_selector)');
        } catch (\Throwable $e) {
            // Index already exists — safe to ignore
        }
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
