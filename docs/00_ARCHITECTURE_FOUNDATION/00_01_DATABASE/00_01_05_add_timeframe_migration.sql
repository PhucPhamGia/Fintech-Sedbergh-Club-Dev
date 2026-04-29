-- Migration: Add timeframe support to btcdatadb
-- Date: 2026-04-11
-- Description: Adds `timeframe` column + composite indexes for multi-timeframe support
-- Safe to run: existing ~1,736 rows auto-tagged 'timeframe=12h' via DEFAULT
-- Reversible: see rollback section at bottom

-- =========================================================================
-- FORWARD MIGRATION
-- =========================================================================

-- Change 1: Add timeframe column
-- Existing rows auto-populate with '12h' via the DEFAULT.
ALTER TABLE btcdatadb
  ADD COLUMN timeframe VARCHAR(4) NOT NULL DEFAULT '12h' AFTER id_coin;

-- Change 2: Drop old weak single-column index
ALTER TABLE btcdatadb DROP INDEX open_time;

-- Change 3a: Composite UNIQUE key
-- - Prevents duplicates at DB level (enables INSERT IGNORE)
-- - Covers queries: WHERE id_coin = ? AND timeframe = ? ORDER BY open_time
ALTER TABLE btcdatadb
  ADD UNIQUE KEY uq_coin_tf_time (id_coin, timeframe, open_time);

-- Change 3b: Composite date index for date-range queries
ALTER TABLE btcdatadb
  ADD KEY idx_coin_tf_date (id_coin, timeframe, date);

-- =========================================================================
-- VERIFICATION
-- =========================================================================

-- All existing rows should be tagged '12h'
SELECT timeframe, COUNT(*) AS row_count
FROM btcdatadb
GROUP BY timeframe;
-- Expected: 12h | 1736 (or whatever row count btcdatadb had)

-- New indexes should exist
SHOW INDEX FROM btcdatadb;
-- Expected keys: PRIMARY, uq_coin_tf_time, idx_coin_tf_date

-- =========================================================================
-- ROLLBACK (if needed)
-- =========================================================================
-- ALTER TABLE btcdatadb DROP INDEX uq_coin_tf_time;
-- ALTER TABLE btcdatadb DROP INDEX idx_coin_tf_date;
-- ALTER TABLE btcdatadb ADD KEY open_time (open_time) USING BTREE;
-- ALTER TABLE btcdatadb DROP COLUMN timeframe;
