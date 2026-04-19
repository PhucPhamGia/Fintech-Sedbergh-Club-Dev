<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddTimeframeToBtcdatadb extends Migration
{
    public function up()
    {
        // Change 1: Add timeframe column.
        // Existing rows auto-tagged '12h' via DEFAULT.
        $this->forge->addColumn('btcdatadb', [
            'timeframe' => [
                'type'       => 'VARCHAR',
                'constraint' => 4,
                'null'       => false,
                'default'    => '12h',
                'after'      => 'id_coin',
            ],
        ]);

        // Change 2: Drop the old single-column open_time index.
        // Replaced by composite indexes below.
        try {
            $this->db->query('ALTER TABLE btcdatadb DROP INDEX open_time');
        } catch (\Throwable $e) {
            // Ignore if index does not exist.
        }

        // Change 3a: Add UNIQUE composite key.
        // - Prevents duplicates at DB level (enables INSERT IGNORE).
        // - Covers the primary query: WHERE id_coin = ? AND timeframe = ? ORDER BY open_time.
        $this->db->query(
            'ALTER TABLE btcdatadb
             ADD UNIQUE KEY uq_coin_tf_time (id_coin, timeframe, open_time)'
        );

        // Change 3b: Add date-range composite index for admin/health queries.
        $this->db->query(
            'ALTER TABLE btcdatadb
             ADD KEY idx_coin_tf_date (id_coin, timeframe, date)'
        );
    }

    public function down()
    {
        // Reverse order: drop new indexes, restore old index, drop column.
        try {
            $this->db->query('ALTER TABLE btcdatadb DROP INDEX uq_coin_tf_time');
        } catch (\Throwable $e) {
        }

        try {
            $this->db->query('ALTER TABLE btcdatadb DROP INDEX idx_coin_tf_date');
        } catch (\Throwable $e) {
        }

        try {
            $this->db->query('ALTER TABLE btcdatadb ADD KEY open_time (open_time) USING BTREE');
        } catch (\Throwable $e) {
        }

        $this->forge->dropColumn('btcdatadb', 'timeframe');
    }
}
