<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Adds 20 PSAR columns to btcdatadb — 4 columns × 5 parameter sets (A–E).
 * See docs/09_ROADMAP/09_03_psar_parameter_research.md for set definitions.
 *
 * To run:   php spark migrate
 * To revert: php spark migrate:rollback
 */
class AddPsarColumnsToBtcdatadb extends Migration
{
    private array $sets = ['a', 'b', 'c', 'd', 'e'];

    public function up(): void
    {
        foreach ($this->sets as $s) {
            $this->forge->addColumn('btcdatadb', [
                "psar_{$s}_value" => ['type' => 'DECIMAL', 'constraint' => '18,8', 'null' => true, 'default' => null],
                "psar_{$s}_trend" => ['type' => 'TINYINT', 'constraint' => 1,      'null' => true, 'default' => null],
                "psar_{$s}_af"    => ['type' => 'DECIMAL', 'constraint' => '6,4',  'null' => true, 'default' => null],
                "psar_{$s}_ep"    => ['type' => 'DECIMAL', 'constraint' => '18,8', 'null' => true, 'default' => null],
            ]);
        }
    }

    public function down(): void
    {
        foreach ($this->sets as $s) {
            $this->forge->dropColumn('btcdatadb', [
                "psar_{$s}_value",
                "psar_{$s}_trend",
                "psar_{$s}_af",
                "psar_{$s}_ep",
            ]);
        }
    }
}
