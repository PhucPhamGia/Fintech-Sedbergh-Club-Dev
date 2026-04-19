# 09_03 — PSAR Parameter Research & Implementation Plan

**Status:** PLANNED
**Last Updated:** 2026-04-19
**Scope:** Full development pipeline for Parabolic SAR — from parameter experimentation to production implementation.
**Parent:** Phase 1.2 in `09_00_PROJECT_MASTER_ROADMAP_2026.md`

---

## Overview

Before committing PSAR values to the database, we need to know *which parameters actually work* on this dataset (4 coins × 6 timeframes). The pipeline below treats parameter selection as a research question first, then a database/implementation question second.

```
Step 1: Define 5 parameter sets
    ↓
Step 2: Calculate PSAR for all DB records (per set)
    ↓
Step 3: Store all 5 sets in the database
    ↓
Step 4: Graph and visualize all sets on the chart
    ↓
Step 5: Compare effectiveness across coins, timeframes, real-time instances
    ↓
Step 6: Assess results → implement the winning set as production PSAR
```

---

## Step 1 — Define 5 Parameter Sets

**Goal:** Cover a wide range of sensitivity — from slow/smooth (fewer signals, fewer whipsaws) to fast/reactive (more signals, more noise). Each set represents a distinct behavioral profile.

| Set | Name | AF Start | AF Step | AF Max | Profile |
|-----|------|----------|---------|--------|---------|
| A | Conservative | 0.01 | 0.01 | 0.10 | Very slow to react. Stays in trends longer. More whipsaw resistance. Best for strong trends. |
| B | Standard (Wilder) | 0.02 | 0.02 | 0.20 | Original Wilder defaults. Baseline reference for all comparisons. |
| C | Moderate | 0.02 | 0.02 | 0.30 | Same start/step as Wilder but higher ceiling. Tightens faster in strong trends. |
| D | Aggressive | 0.03 | 0.03 | 0.30 | Faster acceleration. More signals. More responsive to reversals but more whipsaws. |
| E | Fast | 0.05 | 0.05 | 0.40 | Most reactive. Suitable for high-volatility short timeframes (15m, 30m). Noisy on 12h. |

**Rationale for range:**
- AF Start controls how far the initial SAR is from price — lower = farther away = slower entry
- AF Step controls how fast the SAR tightens — lower = slower trailing stop
- AF Max caps the tightening — lower = SAR never gets too close, fewer exits during volatile candles
- Set A and E are intentionally extreme to bracket the useful range

---

## Step 2 — Calculate PSAR for All DB Records

**Goal:** Run the PSAR algorithm over every record in `btcdatadb` for each of the 5 parameter sets, for every coin × timeframe combination.

**Implementation:** New method `C_Database::PSAR_Batch()` that loops:
```
foreach coin:
  foreach timeframe:
    fetch all rows ORDER BY open_time ASC
    run computePSAR(rows, afStart, afStep, afMax)
    collect {id, psar_value, psar_trend, psar_af, psar_ep} for each row
    updateBatch(results)
```

**Algorithm (standard Parabolic SAR):**
```
Init (from first 2 candles):
  bull = close[1] >= close[0]
  if bull: EP = max(high[0], high[1]),  SAR = min(low[0], low[1])
  else:    EP = min(low[0], low[1]),    SAR = max(high[0], high[1])
  AF = afStart

For each candle i >= 2:
  newSAR = SAR + AF × (EP - SAR)

  if bull:
    newSAR = min(newSAR, low[i-1], low[i-2])      ← SAR cannot be above prior 2 lows
    if low[i] <= newSAR:                            ← reversal
      bull=false, newSAR=EP, EP=low[i], AF=afStart
      newSAR = max(newSAR, high[i-1], high[i-2])   ← clamp after flip
    else if high[i] > EP:
      EP = high[i], AF = min(AF + afStep, afMax)   ← new extreme, accelerate

  else:
    newSAR = max(newSAR, high[i-1], high[i-2])
    if high[i] >= newSAR:                           ← reversal
      bull=true, newSAR=EP, EP=high[i], AF=afStart
      newSAR = min(newSAR, low[i-1], low[i-2])
    else if low[i] < EP:
      EP = low[i], AF = min(AF + afStep, afMax)

  SAR = newSAR
  store: psar_value=SAR, psar_trend=bull, psar_af=AF, psar_ep=EP
```

**Key implementation notes:**
- Must process each coin+timeframe as a separate independent sequence (PSAR is stateful across candles)
- `psar_af` and `psar_ep` are stored so the sequence can be extended incrementally with new candles without recalculating from scratch
- Use `updateBatch()` for performance — do not issue one UPDATE per row

---

## Step 3 — Save to Database

**Goal:** Store all 5 parameter sets in `btcdatadb` as separate column groups.

**DB Schema — new columns per set:**

```sql
-- Set A (Conservative: 0.01 / 0.01 / 0.10)
ALTER TABLE btcdatadb ADD COLUMN psar_a_value DECIMAL(18,8) DEFAULT NULL;
ALTER TABLE btcdatadb ADD COLUMN psar_a_trend TINYINT(1)    DEFAULT NULL;
ALTER TABLE btcdatadb ADD COLUMN psar_a_af    DECIMAL(6,4)  DEFAULT NULL;
ALTER TABLE btcdatadb ADD COLUMN psar_a_ep    DECIMAL(18,8) DEFAULT NULL;

-- Set B (Standard Wilder: 0.02 / 0.02 / 0.20)
ALTER TABLE btcdatadb ADD COLUMN psar_b_value DECIMAL(18,8) DEFAULT NULL;
ALTER TABLE btcdatadb ADD COLUMN psar_b_trend TINYINT(1)    DEFAULT NULL;
ALTER TABLE btcdatadb ADD COLUMN psar_b_af    DECIMAL(6,4)  DEFAULT NULL;
ALTER TABLE btcdatadb ADD COLUMN psar_b_ep    DECIMAL(18,8) DEFAULT NULL;

-- Set C (Moderate: 0.02 / 0.02 / 0.30)
ALTER TABLE btcdatadb ADD COLUMN psar_c_value DECIMAL(18,8) DEFAULT NULL;
ALTER TABLE btcdatadb ADD COLUMN psar_c_trend TINYINT(1)    DEFAULT NULL;
ALTER TABLE btcdatadb ADD COLUMN psar_c_af    DECIMAL(6,4)  DEFAULT NULL;
ALTER TABLE btcdatadb ADD COLUMN psar_c_ep    DECIMAL(18,8) DEFAULT NULL;

-- Set D (Aggressive: 0.03 / 0.03 / 0.30)
ALTER TABLE btcdatadb ADD COLUMN psar_d_value DECIMAL(18,8) DEFAULT NULL;
ALTER TABLE btcdatadb ADD COLUMN psar_d_trend TINYINT(1)    DEFAULT NULL;
ALTER TABLE btcdatadb ADD COLUMN psar_d_af    DECIMAL(6,4)  DEFAULT NULL;
ALTER TABLE btcdatadb ADD COLUMN psar_d_ep    DECIMAL(18,8) DEFAULT NULL;

-- Set E (Fast: 0.05 / 0.05 / 0.40)
ALTER TABLE btcdatadb ADD COLUMN psar_e_value DECIMAL(18,8) DEFAULT NULL;
ALTER TABLE btcdatadb ADD COLUMN psar_e_trend TINYINT(1)    DEFAULT NULL;
ALTER TABLE btcdatadb ADD COLUMN psar_e_af    DECIMAL(6,4)  DEFAULT NULL;
ALTER TABLE btcdatadb ADD COLUMN psar_e_ep    DECIMAL(18,8) DEFAULT NULL;
```

**Total:** 20 new columns (4 per set × 5 sets)

**Files to change:**
- DB migration file (new migration in `app/Database/Migrations/`)
- `M_Coin_Data::$allowedFields` — add all 20 columns
- `C_Database` — new `PSAR_Batch()` method + admin button + route

---

## Step 4 — Graphing and UI

**Goal:** Visualize all 5 PSAR sets on the chart simultaneously so differences in behavior are immediately visible.

**Chart approach:**
- All 5 PSAR sets rendered as scatter series on the same candlestick chart
- Each set gets a distinct color pair (bull dot / bear dot):

| Set | Bull color | Bear color |
|-----|-----------|------------|
| A — Conservative | `#00e676` (green) | `#ff595e` (red) |
| B — Standard | `#40c4ff` (sky) | `#ff9100` (orange) |
| C — Moderate | `#b9f6ca` (mint) | `#ff6d00` (deep orange) |
| D — Aggressive | `#80d8ff` (light blue) | `#ea80fc` (purple) |
| E — Fast | `#ffd740` (amber) | `#ff4081` (pink) |

- Add a toggle panel below the chart: checkboxes to show/hide each set independently
- Default: show Set B (Wilder standard) only, others off

**UI components to add:**
- PSAR set selector panel (checkboxes per set + color legend)
- Active set label on chart legend
- Data table columns: `psar_a`, `psar_b`, `psar_c`, `psar_d`, `psar_e` trend columns (showing ↑/↓)

---

## Step 5 — Compare Effectiveness

**Goal:** Build a scoring function that measures how well each parameter set performs across all coins, all timeframes, and on recent real-time data.

**Metrics to calculate per set, per coin, per timeframe:**

| Metric | Description | Better = |
|--------|-------------|----------|
| **Signal count** | Total PSAR flips over dataset | Context-dependent |
| **Whipsaw rate** | % of flips reversed within 3 candles | Lower |
| **Trend accuracy** | % of flips that led to sustained move (>5 candles same direction) | Higher |
| **Avg profit per signal** | Mean price change from entry to next flip, in % | Higher |
| **Max adverse excursion** | Worst drawdown from entry before exit | Lower |
| **Profit factor** | Gross gains / gross losses across all signals | Higher (>1.5 good) |
| **Consistency score** | StdDev of profit per signal (lower = more consistent) | Lower |

**Implementation — `C_Database::PSAR_Compare()`:**
```php
public function PSAR_Compare(): array
{
    $results = [];
    $sets = ['a', 'b', 'c', 'd', 'e'];

    foreach ($this->get_list_coin() as $coin) {
        foreach (self::TIMEFRAMES as $tf) {
            $rows = // fetch all rows for coin+tf ORDER BY open_time ASC

            foreach ($sets as $set) {
                $signals = $this->extractSignals($rows, "psar_{$set}_trend", "psar_{$set}_value");
                $results[$set][$coin['id_coin']][$tf] = $this->scoreSignals($signals, $rows);
            }
        }
    }

    return $results; // returned to view for display
}

private function extractSignals(array $rows, string $trendCol, string $valueCol): array
{
    // Find every row where psar_trend flips — that's a signal
    // Return [{index, direction, entry_price, psar_value}, ...]
}

private function scoreSignals(array $signals, array $rows): array
{
    // Calculate whipsaw rate, trend accuracy, profit factor, etc.
    // Return [{metric => value}, ...]
}
```

**Output:** A comparison table view (admin only) showing all sets side by side:

```
             | Set A | Set B | Set C | Set D | Set E |
-------------|-------|-------|-------|-------|-------|
BTC 12h      |       |       |       |       |       |
  Signals    |  12   |  18   |  20   |  27   |  41   |
  Whipsaws   | 8%    | 12%   | 14%   | 22%   | 38%   |
  Profit F.  | 2.1   | 1.8   | 1.7   | 1.4   | 0.9   |
BTC 1h       |  ...  |       |       |       |       |
ETH 12h      |  ...  |       |       |       |       |
...
```

**Real-time instances:** For the most recent N candles (e.g. last 30 days), run the same scoring to check if the historical winner also wins on recent data. A set that scores well historically but poorly on recent data may be overfit to old market conditions.

---

## Step 6 — Assess and Implement

**Goal:** Use the Step 5 comparison data to select the best parameter set (or sets) and promote it to the production PSAR used by the signal engine.

**Assessment criteria (in order of importance):**

1. **Profit factor > 1.5** across most coin+timeframe combinations
2. **Whipsaw rate < 20%** — too many quick reversals makes the signal unreliable
3. **Consistent across timeframes** — a set that only works on 12h but fails on 1h is fragile
4. **Consistent across coins** — a set that only works on BTC is not generalizable
5. **Consistent on recent data** — historical winner must hold on last 30 days

**Decision outcomes:**

| Outcome | Condition | Action |
|---------|-----------|--------|
| Single winner | One set clearly wins on most metrics | Rename `psar_X_*` columns to `psar_value`, `psar_trend`, `psar_af`, `psar_ep` (production columns). Drop other 4 sets. |
| Timeframe-specific | Different sets win on different timeframes | Store the winning set per timeframe in a config table. Signal engine selects parameters per timeframe. |
| No clear winner | No set achieves profit factor > 1.5 consistently | Return to Step 1 with a new parameter range informed by the scoring data. Do not implement until a winner is found. |

**Implementation tasks (after winner selected):**

1. Add production columns: `psar_value`, `psar_trend`, `psar_af`, `psar_ep` to `btcdatadb`
2. Copy winning set's data into production columns
3. Drop the 5 experimental column sets (or archive as `psar_exp_*`)
4. Update `C_Database::PSAR()` to use production parameter values
5. Add PSAR to nightly recalculation chain (after each import)
6. Enable PSAR dots on chart for all users (not just admin)
7. Add PSAR flip detection to signal engine (Phase 4.1 — binary gate architecture)
8. Document the winning parameters and reasoning in this file

**After implementation, update this file with:**
- Which set won and why
- Scores per coin/timeframe
- Any anomalies observed
- Date of production deployment

---

## Dependencies

| Dependency | Status |
|-----------|--------|
| `btcdatadb` has `timeframe` column | DONE (migration deployed) |
| Data imported for all 6 timeframes | Partial — 12h done, others need import |
| `M_Coin_Data::updateBatch()` | DONE |
| Admin route + button infrastructure | DONE (pattern established in C_Database) |

---

## Effort Estimate

| Step | Effort |
|------|--------|
| Step 1 — Parameter sets definition | Done (in this doc) |
| Step 2 — PHP calculation function | 3–4 hours |
| Step 3 — DB migration (20 columns) | 1–2 hours |
| Step 4 — Graphing + toggle UI | 4–6 hours |
| Step 5 — Comparison function + table view | 6–8 hours |
| Step 6 — Assessment + production implementation | 2–4 hours |
| **Total** | **~16–24 hours** |
