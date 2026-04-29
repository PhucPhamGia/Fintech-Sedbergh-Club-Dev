# 09_02 — Multi-Timeframe Candle Architecture

**Created:** 2026-04-11
**Last Updated:** 2026-04-11 (r02 — ALTER existing `btcdatadb` instead of creating new table)
**Status:** PROPOSED — Architecture decision for multi-timeframe data storage and import
**Scope:** Extend single-timeframe (12h only) system to 6-timeframe support (15m, 30m, 1h, 3h, 6h, 12h)
**Principle:** Minimal DB changes. Keep existing table, add the missing pieces.

---

## Quick Summary

| What | Current System | New System |
|---|---|---|
| Timeframes | 12h only | 15m, 30m, 1h, 3h, 6h, 12h |
| Table | `btcdatadb` (no timeframe column) | `btcdatadb` + new `timeframe` column + composite indexes |
| Rows/day | 8 (4 coins × 2) | 728 (4 coins × 182) |
| Rows/year | ~2,920 | ~265,720 |
| Import | 2 methods (historical + daily), hardcoded `12h` | 1 generic method, timeframe as parameter |
| Model | `M_Coin_Data` (queries all rows) | `M_Coin_Data` with timeframe parameter on query methods |
| Indicators | Stored in same row (ma20, ma50, ...) | Same — indicators are per-candle, per-timeframe |
| Dedup | PHP `countAllResults()` per row | DB-level via new `UNIQUE KEY (id_coin, timeframe, open_time)` + `INSERT IGNORE` |

---

## 1. Approach: ALTER Existing Table

The existing `btcdatadb` already has the correct OHLCV structure. We just need to add a `timeframe` column and fix the indexes.

| Option | Approach | Verdict |
|---|---|---|
| **A. ALTER btcdatadb** (CHOSEN) | Add `timeframe` column with `DEFAULT '12h'`, add composite indexes | **Simplest.** Existing data auto-tagged as '12h', no data migration, no table rename |
| B. Create new table | Build `btcdatadb` from scratch, copy data over, transition period | Over-engineered — adds data migration step and table deprecation |
| C. Separate tables per TF | `kline_15m`, `kline_30m`, ... | Over-engineered — 6 models, duplicated code |

**Decision: Option A.** Two `ALTER TABLE` statements. Existing rows auto-get `timeframe='12h'` via the column default. No data migration, no new table, no transition.

---

## 2. Database Schema Changes

### 2.1 The ALTER Statements

```sql
-- Change 1: Add timeframe column
-- Existing ~1,736 rows automatically get '12h' via DEFAULT.
ALTER TABLE btcdatadb 
  ADD COLUMN timeframe VARCHAR(4) NOT NULL DEFAULT '12h' AFTER id_coin;

-- Change 2: Replace weak single-column index with composite indexes
ALTER TABLE btcdatadb DROP INDEX open_time;

ALTER TABLE btcdatadb 
  ADD UNIQUE KEY uq_coin_tf_time (id_coin, timeframe, open_time);

ALTER TABLE btcdatadb 
  ADD KEY idx_coin_tf_date (id_coin, timeframe, date);
```

### 2.2 What the Schema Looks Like After

```sql
-- btcdatadb after ALTER (pseudo-DDL, current state + changes)
CREATE TABLE `btcdatadb` (
  `id`                  INT(11)       NOT NULL AUTO_INCREMENT,
  `id_coin`             INT(11)       NOT NULL,
  `timeframe`           VARCHAR(4)    NOT NULL DEFAULT '12h',  -- NEW
  `date`                DATE          NOT NULL,
  `open_time`           BIGINT(20)    NOT NULL,
  `open_price`          DECIMAL(18,8) NOT NULL,
  `high_price`          DECIMAL(18,8) NOT NULL,
  `low_price`           DECIMAL(18,8) NOT NULL,
  `close_price`         DECIMAL(18,8) NOT NULL,
  `volume`              DECIMAL(18,8) NOT NULL,
  `close_time`          BIGINT(20)    NOT NULL,
  `quote_volume`        DECIMAL(18,8) NOT NULL,
  `number_of_trades`    INT(11)       NOT NULL,
  `taker_base_volume`   DECIMAL(18,8) NOT NULL,
  `taker_quote_volume`  DECIMAL(18,8) NOT NULL,
  `ma20`                DOUBLE        DEFAULT NULL,
  `ma50`                DOUBLE        DEFAULT NULL,
  
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_coin_tf_time` (`id_coin`, `timeframe`, `open_time`),  -- NEW
  KEY `idx_coin_tf_date` (`id_coin`, `timeframe`, `date`)              -- NEW
  -- Old `open_time` index REMOVED (replaced by composite)
) ENGINE=InnoDB;
```

### 2.3 Key Design Decisions

**`UNIQUE KEY (id_coin, timeframe, open_time)`** — Does three things:
1. **Prevents duplicates** at DB level (no more PHP `countAllResults()` check)
2. **Covers the primary query pattern**: `WHERE id_coin = ? AND timeframe = ? ORDER BY open_time`
3. **Enables `INSERT IGNORE`** — attempt insert, silently skip duplicates (faster than check-then-insert)

**Indicator columns (`ma20`, `ma50`) stay in the same table.** For ~265k rows/year this is fine. Add more indicator columns (psar_value, adx_14, etc.) in later phases via separate ALTER statements.

**`timeframe` is VARCHAR(4), not ENUM.** Easier to add new timeframes later (1m, 4h, 1d, 1w) without schema change.

**Rollback:** If anything goes wrong, revert with:
```sql
ALTER TABLE btcdatadb DROP INDEX uq_coin_tf_time;
ALTER TABLE btcdatadb DROP INDEX idx_coin_tf_date;
ALTER TABLE btcdatadb DROP COLUMN timeframe;
ALTER TABLE btcdatadb ADD KEY open_time (open_time) USING BTREE;
```

### 2.3 Data Volume Estimates

| Timeframe | Candles/Day | Per Coin/100d | Per Coin/Year | All 4 Coins/Year |
|---|---|---|---|---|
| 15m | 96 | 9,600 | 35,040 | 140,160 |
| 30m | 48 | 4,800 | 17,520 | 70,080 |
| 1h | 24 | 2,400 | 8,760 | 35,040 |
| 3h | 8 | 800 | 2,920 | 11,680 |
| 6h | 4 | 400 | 1,460 | 5,840 |
| 12h | 2 | 200 | 730 | 2,920 |
| **Total** | **182** | **18,200** | **66,430** | **265,720** |

~265k rows/year is trivial for MySQL. InnoDB handles tens of millions with proper indexes. The composite unique key ensures all queries are index-covered.

**Storage estimate:** ~265k rows × ~200 bytes/row ≈ **~50 MB/year**. Negligible.

---

## 3. Import Architecture

### 3.1 Single Generic Import Method

Replace the two current methods (`Binance_Import` + `Binance_Daily_Import`) with one:

```php
/**
 * Import kline data from Binance for specified timeframes and time range.
 *
 * @param array  $timeframes  e.g. ['15m','30m','1h','3h','6h','12h']
 * @param int    $days        lookback period in days (100 for historical, 1 for daily)
 */
public function import(array $timeframes, int $days): array
{
    $coins = $this->M_Coin_Data->get_list_coin();
    $endTime = round(microtime(true) * 1000);
    $startTime = strtotime("-{$days} days") * 1000;
    $results = [];

    foreach ($coins as $coin) {
        foreach ($timeframes as $tf) {
            $inserted = $this->fetchAndStore($coin, $tf, $startTime, $endTime);
            $results[] = [
                'coin' => $coin['coinname'],
                'timeframe' => $tf,
                'inserted' => $inserted,
            ];
        }
    }
    return $results;
}

/**
 * Fetch from Binance API and bulk-insert into btcdatadb.
 * Uses INSERT IGNORE to skip duplicates at DB level (no PHP dedup check).
 */
private function fetchAndStore(array $coin, string $tf, int $startTime, int $endTime): int
{
    $inserted = 0;
    $symbol = $coin['coinname'];
    $current = $startTime;

    // Binance returns max 1000 candles per call. Paginate if needed.
    while ($current < $endTime) {
        $url = "https://api.binance.com/api/v3/klines"
             . "?symbol={$symbol}&interval={$tf}"
             . "&startTime={$current}&endTime={$endTime}&limit=1000";

        $klines = $this->callBinanceApi($url);
        if (empty($klines)) break;

        $batch = [];
        foreach ($klines as $k) {
            $batch[] = [
                'id_coin'            => $coin['id_coin'],
                'timeframe'          => $tf,
                'open_time'          => $k[0],
                'close_time'         => $k[6],
                'date'               => date('Y-m-d', $k[0] / 1000),
                'open_price'         => $k[1],
                'high_price'         => $k[2],
                'low_price'          => $k[3],
                'close_price'        => $k[4],
                'volume'             => $k[5],
                'quote_volume'       => $k[7],
                'number_of_trades'   => $k[8],
                'taker_base_volume'  => $k[9],
                'taker_quote_volume' => $k[10],
            ];
        }

        // INSERT IGNORE — DB-level dedup via UNIQUE KEY, no PHP check needed
        $this->M_Coin_Data->insertBatchIgnore($batch);
        $inserted += count($batch);

        // Move pagination cursor to after last candle
        $current = end($klines)[6] + 1;  // close_time + 1ms

        // If we got fewer than 1000, we've reached the end
        if (count($klines) < 1000) break;
    }
    return $inserted;
}

/**
 * Reusable cURL wrapper with error handling.
 */
private function callBinanceApi(string $url): array
{
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    $response = curl_exec($ch);

    if ($response === false) {
        log_message('error', 'Binance API cURL error: ' . curl_error($ch));
        curl_close($ch);
        return [];
    }
    curl_close($ch);

    $data = json_decode($response, true);
    if (isset($data['code'])) {
        log_message('error', "Binance API error: {$data['code']} - {$data['msg']}");
        return [];
    }
    return is_array($data) ? $data : [];
}
```

### 3.2 Controller Endpoints

```php
// Routes
$routes->post('/import/historical', 'C_Import::historical');     // All TFs, 100 days
$routes->post('/import/daily',      'C_Import::daily');          // All TFs, today
$routes->post('/import/custom',     'C_Import::custom');         // Specific TF + days

// Controller: C_Import.php (NEW — separated from C_Database)
class C_Import extends BaseController
{
    const ALL_TIMEFRAMES = ['15m', '30m', '1h', '3h', '6h', '12h'];
    const VALID_TIMEFRAMES = ['15m', '30m', '1h', '3h', '6h', '12h'];

    // Import all timeframes, last 100 days
    public function historical()
    {
        $results = $this->import(self::ALL_TIMEFRAMES, 100);
        return redirect()->to('/database/1/50')->with('success', 'Historical import complete');
    }

    // Import all timeframes, today only
    public function daily()
    {
        $results = $this->import(self::ALL_TIMEFRAMES, 1);
        return redirect()->to('/database/1/50')->with('success', 'Daily import complete');
    }

    // Import specific timeframes and days (for admin flexibility)
    public function custom()
    {
        $tf   = $this->request->getPost('timeframes');  // e.g. ['1h', '12h']
        $days = (int) $this->request->getPost('days');   // e.g. 30
        // ... validate, then import
    }
}
```

### 3.3 API Call Budget

| Scenario | Calls | Time (est.) |
|---|---|---|
| Daily import (all TFs, 4 coins) | 24 calls (1 per TF per coin) | ~5 seconds |
| Historical 100d (all TFs, 4 coins) | ~56 calls (15m needs pagination) | ~30 seconds |
| Historical 100d (12h only, 4 coins) | 4 calls | ~2 seconds |

Binance limit: 1,200 requests/minute. We never come close.

---

## 4. Model Architecture

### 4.1 Extending Existing Model: `M_Coin_Data`

Update the existing `app/Models/M_Coin_Data.php` — no new model needed. Just add `timeframe` to `allowedFields` and add new query methods. Keep existing methods for backward compatibility (they can be deprecated later).

```php
class M_Coin_Data extends Model
{
    protected $table        = 'btcdatadb';
    protected $primaryKey   = 'id';
    protected $allowedFields = [
        'id_coin', 'timeframe', 'open_time', 'close_time', 'date',  // 'timeframe' ADDED
        'open_price', 'high_price', 'low_price', 'close_price',
        'volume', 'quote_volume', 'number_of_trades',
        'taker_base_volume', 'taker_quote_volume',
        'ma20', 'ma50',  // indicator columns stay
    ];

    /**
     * Bulk insert with IGNORE — skips duplicates via UNIQUE KEY.
     * Replaces the slow PHP-side countAllResults() dedup check.
     */
    public function insertBatchIgnore(array $data): bool
    {
        if (empty($data)) return false;
        $builder = $this->db->table($this->table);
        // Build INSERT IGNORE manually since CI4 doesn't have native support
        $keys = array_keys($data[0]);
        $sql = "INSERT IGNORE INTO {$this->table} (" . implode(',', $keys) . ") VALUES ";
        $values = [];
        foreach ($data as $row) {
            $escaped = array_map(fn($v) => $this->db->escape($v), $row);
            $values[] = '(' . implode(',', $escaped) . ')';
        }
        $sql .= implode(',', $values);
        return $this->db->query($sql);
    }

    // === CORE QUERY METHODS ===

    /**
     * Get candles for a coin + timeframe, ordered by time.
     * This is THE primary query — covered by the UNIQUE index.
     */
    public function getCandles(int $coinId, string $tf, int $limit = 0, string $order = 'ASC'): array
    {
        $builder = $this->where('id_coin', $coinId)
                        ->where('timeframe', $tf)
                        ->orderBy('open_time', $order);
        return $limit > 0 ? $builder->findAll($limit) : $builder->findAll();
    }

    /**
     * Get candles for last N days.
     */
    public function getCandlesByDays(int $coinId, string $tf, int $days, string $order = 'ASC'): array
    {
        $startTime = (time() - ($days * 86400)) * 1000;  // ms
        return $this->where('id_coin', $coinId)
                    ->where('timeframe', $tf)
                    ->where('open_time >=', $startTime)
                    ->orderBy('open_time', $order)
                    ->findAll();
    }

    /**
     * Get OHLC data for candlestick chart rendering.
     */
    public function getCandlestickData(int $coinId, string $tf, int $limit): array
    {
        return $this->select('date, open_time, open_price, high_price, low_price, close_price, volume')
                    ->where('id_coin', $coinId)
                    ->where('timeframe', $tf)
                    ->orderBy('open_time', 'DESC')
                    ->findAll($limit);
    }

    /**
     * Get latest candle for a coin + timeframe (useful for dashboard).
     */
    public function getLatestCandle(int $coinId, string $tf): ?array
    {
        return $this->where('id_coin', $coinId)
                    ->where('timeframe', $tf)
                    ->orderBy('open_time', 'DESC')
                    ->first();
    }

    /**
     * Count candles per timeframe for a coin (data health check).
     */
    public function countByTimeframe(int $coinId): array
    {
        return $this->db->table($this->table)
            ->select('timeframe, COUNT(*) as cnt, MIN(date) as first_date, MAX(date) as last_date')
            ->where('id_coin', $coinId)
            ->groupBy('timeframe')
            ->get()
            ->getResultArray();
    }

    // Coin list (same as current M_Coin_Data)
    public function getCoins(): array
    {
        return $this->db->table('tbl_coin')->get()->getResultArray();
    }

    public function getCoinName(int $coinId): ?string
    {
        return $this->db->table('tbl_coin')
            ->select('coinname')
            ->where('id_coin', $coinId)
            ->get()
            ->getRow('coinname');
    }
}
```

### 4.2 Query Pattern — Every Query Hits the Index

Every method above uses `WHERE id_coin = ? AND timeframe = ?` — this is a prefix match on the composite unique key `(id_coin, timeframe, open_time)`. MySQL uses the index for filtering AND ordering. No full table scan ever happens.

```
-- Example EXPLAIN output (what MySQL does internally):
-- getCandles(1, '1h', 100)
SELECT * FROM btcdatadb
  WHERE id_coin = 1 AND timeframe = '1h'
  ORDER BY open_time ASC LIMIT 100;
-- → Uses index: uq_coin_tf_time (range scan on open_time within coin+tf)
-- → Estimated rows scanned: 100 (exactly what we need)
```

---

## 5. Indicator Storage (Separate Table)

### 5.1 Why Separate from `btcdatadb`

| Concern | btcdatadb + indicator columns | Separate `indicator_data` table |
|---|---|---|
| Table width | Grows with every new indicator (21+ columns planned) | Stays lean — pure OHLCV |
| ALTER TABLE | Locks table during migration on large data | Never needs to ALTER btcdatadb |
| Insert speed | Wider rows = slower inserts | btcdatadb inserts stay fast |
| Indicator recalculation | Updates same row (locks during batch update) | Independent writes, no contention |
| Adding new indicators | Migration + model update + allowedFields | Just insert rows, no schema change |

### 5.2 Schema: `indicator_data`

```sql
CREATE TABLE `indicator_data` (
  `id`         INT(11)       NOT NULL AUTO_INCREMENT,
  `kline_id`   INT(11)       NOT NULL,  -- FK to btcdatadb.id
  `name`       VARCHAR(20)   NOT NULL,  -- 'ma20', 'psar_value', 'rsi_14', etc.
  `value`      DECIMAL(30,8) DEFAULT NULL,
  
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_kline_indicator` (`kline_id`, `name`),
  KEY `idx_name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
```

**But wait** — this is an EAV (Entity-Attribute-Value) pattern, which has known downsides (harder to query multiple indicators at once, JOIN overhead). For a small project with junior developers, **the simpler approach is better: keep indicators as columns on btcdatadb.**

### 5.3 Recommended: Indicators Stay as Columns (Simpler)

For this project's scale (~265k rows/year), the performance difference is negligible. Keeping indicators as columns is:
- Easier to query: `SELECT close_price, ma20, rsi_14 FROM btcdatadb WHERE ...`
- Easier to understand for junior developers
- No JOINs needed
- `ALTER TABLE ADD COLUMN` on 265k rows takes <1 second

**Decision: Keep indicators as columns on `btcdatadb`.** The separate table is over-engineering for this scale.

Updated `btcdatadb` with indicator columns:

```sql
-- Add indicator columns as needed (same as current roadmap plan)
ALTER TABLE btcdatadb ADD COLUMN ma20 DOUBLE DEFAULT NULL;
ALTER TABLE btcdatadb ADD COLUMN ma50 DOUBLE DEFAULT NULL;
ALTER TABLE btcdatadb ADD COLUMN psar_value DECIMAL(18,8) DEFAULT NULL;
ALTER TABLE btcdatadb ADD COLUMN psar_trend TINYINT(1) DEFAULT NULL;
-- ... (remaining 17 indicator columns from roadmap)
```

---

## 6. Migration Plan (Incremental)

### Step 1: ALTER the database (the 3 statements from Section 2.1)
Run via CI4 migration (`php spark migrate`), phpMyAdmin, or SSH + MySQL CLI. Idempotent — existing 12h data auto-tagged via `DEFAULT '12h'`.

### Step 2: Update `M_Coin_Data` model
- Add `timeframe` to `$allowedFields`
- Add new methods: `insertBatchIgnore()`, `getCandles($coinId, $tf, ...)`, `countByTimeframe()`
- Keep existing methods for backward compatibility (they'll still return 12h data since that's all that exists initially)

### Step 3: Create `C_Import` controller
- Generic `import($timeframes, $days)` method
- Reusable `callBinanceApi()` cURL wrapper
- Routes: `/import/historical`, `/import/daily`, `/import/cron`

### Step 4: Import historical data for new timeframes
- Run `/import/historical` → fetches 100 days of 15m, 30m, 1h, 3h, 6h, 12h data
- `INSERT IGNORE` handles existing 12h rows automatically (no duplicates)
- Verify data with `M_Coin_Data::countByTimeframe()`

### Step 5: Update views and controllers
- Update `C_View::Database()` to accept timeframe parameter
- Update `V_Database.php` with timeframe selector buttons
- Update routes: `/database/{coin_id}/{timeframe}/{days}`

### Step 6: Set up cron schedule
- Configure per-timeframe cron jobs (see Section 8)
- Verify first few automated imports succeed

**Rollback at any step:** Revert the 3 ALTER statements (see Section 2.3). Existing 12h data remains unchanged throughout.

---

## 7. Updated Routes

```php
// New routes with timeframe support
$routes->get('/database/(:num)/(:alpha)/(:num)', 'C_View::Database/$1/$2/$3');
// Example: /database/1/1h/50  → BTC, 1-hour candles, last 50 days

// Import routes (admin only)
$routes->post('/import/historical', 'C_Import::historical');
$routes->post('/import/daily',     'C_Import::daily');
$routes->post('/import/custom',    'C_Import::custom');
```

### URL Pattern

```
/database/{coin_id}/{timeframe}/{days}

Examples:
  /database/1/12h/50    → BTC 12h candles, 50 days (current behavior)
  /database/1/1h/7      → BTC 1h candles, 7 days
  /database/5/15m/3     → ETH 15m candles, 3 days
  /database/3/6h/30     → SOL 6h candles, 30 days
```

---

## 8. Cron Schedule

```bash
# Daily imports — stagger to avoid rate limit spikes

# 15m: every 15 minutes (high frequency)
*/15 * * * * curl -s "https://giaphuc.thuytrieu.vn/public/import/cron?tf=15m&key=SECRET"

# 30m: every 30 minutes
*/30 * * * * curl -s "https://giaphuc.thuytrieu.vn/public/import/cron?tf=30m&key=SECRET"

# 1h: every hour
0 * * * * curl -s "https://giaphuc.thuytrieu.vn/public/import/cron?tf=1h&key=SECRET"

# 3h: every 3 hours
0 */3 * * * curl -s "https://giaphuc.thuytrieu.vn/public/import/cron?tf=3h&key=SECRET"

# 6h: every 6 hours
0 */6 * * * curl -s "https://giaphuc.thuytrieu.vn/public/import/cron?tf=6h&key=SECRET"

# 12h: every 12 hours
0 0,12 * * * curl -s "https://giaphuc.thuytrieu.vn/public/import/cron?tf=12h&key=SECRET"

# Indicators: recalculate after each 12h import (covers all TFs)
5 0,12 * * * curl -s "https://giaphuc.thuytrieu.vn/public/import/indicators?key=SECRET"
```

**API key (`SECRET`)**: Prevents unauthorized imports. Checked in controller before processing. Stored in `.env`.

---

## 9. Frontend: Timeframe Selector

The chart view gets a timeframe dropdown:

```
┌─────────────────────────────────────────────────────────┐
│  BTC/USDT    [15m ▾] [30m] [1h] [3h] [6h] [12h]      │
│                                                         │
│  ┌─────────────────────────────────────────────────┐   │
│  │                                                 │   │
│  │           Candlestick Chart                     │   │
│  │           (PSAR dots + MA lines)                │   │
│  │                                                 │   │
│  └─────────────────────────────────────────────────┘   │
│                                                         │
│  ┌──────────────┐  ┌──────────────┐                    │
│  │   RSI (14)   │  │   ADX (14)   │                    │
│  └──────────────┘  └──────────────┘                    │
└─────────────────────────────────────────────────────────┘
```

Clicking a timeframe button navigates to `/database/{coin_id}/{tf}/{days}`. No page reload needed if using AJAX (future enhancement).

---

## 10. Indicator Calculation: Timeframe-Aware

Indicators calculate per-timeframe. MA20 on 15m candles and MA20 on 12h candles are completely different values, stored on different rows.

```php
// Indicator methods accept timeframe parameter
public function calculateMA20(int $coinId, string $tf): void
{
    $rows = $this->M_Coin_Data->getCandles($coinId, $tf);
    // ... same sliding window algorithm, operates on rows for this TF only
}

// Cron chain: calculate indicators for all TFs
public function recalculateAll(): void
{
    $coins = $this->M_Coin_Data->getCoins();
    $timeframes = ['15m', '30m', '1h', '3h', '6h', '12h'];
    
    foreach ($coins as $coin) {
        foreach ($timeframes as $tf) {
            $this->calculateMA20($coin['id_coin'], $tf);
            $this->calculateMA50($coin['id_coin'], $tf);
            $this->calculatePSAR($coin['id_coin'], $tf);
            $this->calculateADX_ATR($coin['id_coin'], $tf);
            $this->calculateRSI($coin['id_coin'], $tf);
            $this->calculateOBV($coin['id_coin'], $tf);
        }
    }
}
```

**Performance note:** Recalculating all indicators for all coins and all timeframes (~72,800 rows for 100 days) takes seconds in PHP. The sliding window algorithms are O(n) per indicator. Not a bottleneck.

---

## 11. Summary: What Changes, What Stays

| Component | Changes? | Details |
|---|---|---|
| **Database** | ALTER existing | `btcdatadb` gets `timeframe` column + composite unique key (2 ALTER statements) |
| **Model** | UPDATE existing | `M_Coin_Data` — add `timeframe` to `$allowedFields`, add new timeframe-aware methods |
| **Import controller** | NEW | `C_Import` — separated from `C_Database`, generic import method |
| **View controller** | UPDATE | `C_View::Database()` accepts timeframe in URL |
| **Chart view** | UPDATE | `V_Database.php` — add timeframe selector buttons |
| **Routes** | UPDATE | `/database/{coin}/{tf}/{days}` |
| **Indicators** | UPDATE | All indicator methods accept timeframe parameter |
| **Cron** | NEW | Per-timeframe cron schedule (15m to 12h) |
| **tbl_coin** | NO CHANGE | Same 4 coins |
| **Auth system** | NO CHANGE | Same login/roles |
| **Existing 12h data** | NO CHANGE | 1,736 rows auto-tagged `timeframe='12h'` via column DEFAULT |

---

## 12. File Changes Summary

| File | Action | What |
|---|---|---|
| `app/Database/Migrations/XXXX_AddTimeframeToBtcdatadb.php` | CREATE | CI4 migration: `up()` runs 3 ALTER statements, `down()` reverts |
| `app/Models/M_Coin_Data.php` | UPDATE | Add `'timeframe'` to `$allowedFields`, add `insertBatchIgnore()`, `getCandles($coinId, $tf, ...)`, `countByTimeframe()` |
| `app/Controllers/C_Import.php` | CREATE | New import controller with generic `import($tfs, $days)` method + `callBinanceApi()` wrapper |
| `app/Controllers/C_View.php` | UPDATE | Add timeframe parameter to `Database()` method |
| `app/Controllers/C_Database.php` | UPDATE | Indicator methods accept timeframe parameter; remove import logic (moved to C_Import) |
| `app/Views/V_Database.php` | UPDATE | Add timeframe selector UI |
| `app/Config/Routes.php` | UPDATE | New route pattern with timeframe segment |

---

## Lien ket

- [Project Master Roadmap](09_00_PROJECT_MASTER_ROADMAP_2026.md)
- [Auto Trading Reference](09_01_auto_trading_systems_reference.md)
- [Database Schema](../00_ARCHITECTURE_FOUNDATION/00_01_DATABASE/00_01_01_btcdatadb_kline_data.sql)
