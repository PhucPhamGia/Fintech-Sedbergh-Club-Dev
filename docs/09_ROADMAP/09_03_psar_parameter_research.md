# 09_03 — PSAR Parameter Research & Backtesting System

**Status:** IN PROGRESS
**Last Updated:** 2026-04-27
**Scope:** Full pipeline — parameter experimentation → backtesting → production implementation.
**Parent:** Phase 1.2 + Phase 5.3 in `09_00_PROJECT_MASTER_ROADMAP_2026.md`

---

## Overview

Two sequential concerns:

1. **Research pipeline (Steps 1–6):** Which PSAR parameter set works best on this dataset?  
   Five sets calculated, stored, and visualized — then formally scored via the backtesting system below.

2. **Backtesting system (Phases 1–2):** A rigorous evaluation that generates a repeatable, defensible answer.  
   Phase 1 is a fixed-weight scorer. Phase 2 adds regime awareness. Build in order.

```
Step 1: Define 5 parameter sets
    ↓
Step 2: Calculate PSAR for all DB records (per set)           ← DONE
    ↓
Step 3: Store all 5 sets in the database
    ↓
Step 4: Graph and visualize all sets on the chart
    ↓
Step 5: Score via Backtesting System (Phase 1 → Phase 2)
    ↓
Step 6: Assess results → implement winning set as production PSAR
```

---

## Step 1 — Define 5 Parameter Sets

**Goal:** Cover a wide range of sensitivity — from slow/smooth to fast/reactive.

| Set | Name | AF Start | AF Step | AF Max | Profile |
|-----|------|----------|---------|--------|---------|
| A | Conservative | 0.01 | 0.01 | 0.10 | Very slow. Stays in trends longer. More whipsaw resistance. Best for strong trends. |
| B | Standard (Wilder) | 0.02 | 0.02 | 0.20 | Original Wilder defaults. Baseline reference for all comparisons. |
| C | Moderate | 0.02 | 0.02 | 0.30 | Same start/step as Wilder but higher ceiling. Tightens faster in strong trends. |
| D | Aggressive | 0.03 | 0.03 | 0.30 | Faster acceleration. More signals. More responsive but more whipsaws. |
| E | Fast | 0.05 | 0.05 | 0.40 | Most reactive. Suitable for high-volatility short timeframes (15m, 30m). Noisy on 12h. |

**Rationale for range:**
- AF Start controls how far the initial SAR is from price — lower = farther away = slower entry
- AF Step controls how fast the SAR tightens — lower = slower trailing stop
- AF Max caps the tightening — lower = SAR never gets too close, fewer exits during volatile candles
- Set A and E are intentionally extreme to bracket the useful range

---

## Step 2 — Calculate PSAR for All DB Records

**Status: DONE** — `C_Database::PSAR_Batch()` implemented; all 5 sets calculated for all records.

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
- Must process each coin+timeframe as a separate independent sequence (PSAR is stateful)
- `psar_af` and `psar_ep` are stored so the sequence can be extended incrementally without recalculating from scratch
- Use `updateBatch()` — do not issue one UPDATE per row

---

## Step 3 — Save to Database

**Goal:** Store all 5 parameter sets in `btcdatadb` as separate column groups.

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
- DB migration file (`app/Database/Migrations/`)
- `M_Coin_Data::$allowedFields` — add all 20 columns
- `C_Database` — `PSAR_Batch()` method + admin button + route

---

## Step 4 — Graphing and UI

**Goal:** Visualize all 5 PSAR sets on the chart simultaneously.

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

- Toggle panel below the chart: checkboxes to show/hide each set independently
- Default: show Set B (Wilder standard) only, others off

**UI components:**
- PSAR set selector panel (checkboxes per set + color legend)
- Active set label on chart legend
- Data table columns: `psar_a` through `psar_e` trend columns (showing ↑/↓)

---

## Step 5 — Backtesting System

Step 5 has two phases. **Build Phase 1 first and validate it before starting Phase 2.**

---

### Phase 1 — Fixed-Weight Evaluation (BUILD FIRST)

**Purpose:** Establish a defensible baseline score for each parameter set across all coin/timeframe combinations. No regime complexity yet.

**Inputs:**
- 5 PSAR parameter sets (A–E)
- 4 coins (BTC, ETH, SOL, BNB)
- 6 timeframes (15m, 30m, 1h, 3h, 6h, 12h)
- 1 year of historical OHLCV data

**Modules to build:**

#### Module 1 — Metrics Calculator

From the trade list for a given set × coin × timeframe, compute:

| Metric | Definition |
|--------|------------|
| **Total PnL** | % return over all trades in the period |
| **Win Rate** | % of trades that closed with profit |
| **MDD** | Maximum Drawdown — largest peak-to-trough equity decline % |
| **Profit Factor** | Gross profit / gross loss |
| **Trade Count** | Number of completed trades |

#### Module 2 — Scoring Function

Normalize each metric to 0–10, then combine with fixed weights:

```
Score = 0.35 × ProfitFactor + 0.30 × (1/MDD) + 0.20 × PnL + 0.15 × WinRate
```

**Normalization thresholds (linear interpolation between breakpoints):**

| Metric | 0 pts | 5 pts | 10 pts |
|--------|-------|-------|--------|
| Profit Factor | < 1.0 | 1.5 | ≥ 2.5 |
| Win Rate | < 30% | 50% | ≥ 65% |
| MDD | > 40% | 20% | < 10% |
| PnL | < 0% | 10% | ≥ 30% |

> **Note:** MDD is inverted — lower drawdown = higher score. The 1/MDD in the formula reflects that.

#### Module 3 — Aggregator / Reporter

Runs the full matrix [5 sets × 4 coins × 6 timeframes] and outputs a comparison table:

```
             | Set A | Set B | Set C | Set D | Set E | WINNER
-------------|-------|-------|-------|-------|-------|-------
BTC  12h     |  6.2  |  7.1  |  7.4  |  5.8  |  4.1  |   C
BTC   1h     |  5.5  |  6.0  |  6.3  |  6.8  |  5.2  |   D
ETH  12h     |  ...  |       |       |       |       |
...
```

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

    return $results;
}

private function extractSignals(array $rows, string $trendCol, string $valueCol): array
{
    // Find every row where psar_trend flips — that's a signal entry
    // Return [{index, direction, entry_price, psar_value}, ...]
}

private function scoreSignals(array $signals, array $rows): array
{
    // Compute PnL, WinRate, MDD, ProfitFactor, TradeCount
    // Normalize each metric to 0-10
    // Return weighted score + raw metrics
}
```

**Validation — Train / Test Split:**

- **Training set:** First 9 months of the 1-year dataset
- **Test set:** Final 3 months (out-of-sample, not used for selection)
- **Process:** Select winner on training data → verify winner holds on test data
- **Minimum trades gate:** Require ≥ 30 completed trades per backtest. If a set × coin × timeframe combination produces fewer than 30 trades, mark it as **INSUFFICIENT DATA** and exclude from ranking.

**A set that performs well on training but fails out-of-sample is disqualified.**

---

### Phase 2 — Regime-Aware Refinement (BUILD AFTER PHASE 1 IS VALIDATED)

**Purpose:** Check whether adding market regime context produces measurably better results than the fixed-weight scorer. Only adopt if it beats Phase 1 on out-of-sample data.

**New modules:**

#### Module 4 — Regime Classifier

Using ADX(14) + ATR%(14) per candle:

| Regime | Condition |
|--------|-----------|
| **VOLATILE** | ATR% > 80th percentile (per-coin calibrated) |
| **TRENDING** | ADX > 25 |
| **SIDEWAYS** | ADX < 20 |

Implementation rules:
- Use rolling window classification
- Apply 3-window confirmation rule to smooth regime transitions (regime must persist 3 candles before being recorded)
- ATR% = ATR_14 / close_price × 100

#### Module 5 — Trade-Regime Tagger

For each trade in the trade list, record the market regime at the candle when the trade was entered. Each trade carries a `regime` label.

#### Module 6 — Regime-Specific Scoring

Different metric weights per regime:

| Regime | Profit Factor weight | MDD weight | PnL weight | Win Rate weight |
|--------|---------------------|------------|------------|-----------------|
| TRENDING | 0.40 | 0.25 | 0.20 | 0.15 |
| SIDEWAYS | 0.25 | 0.40 | 0.20 | 0.15 |
| VOLATILE | 0.30 | 0.40 | 0.15 | 0.15 |

**Final score formula:**
```
FinalScore = Σ(regime_score × regime_frequency_in_period)
```

**Edge case:** Skip any regime with < 10 trades. Redistribute its weight proportionally to the remaining regimes.

#### A/B Comparison Gate

Before adopting Phase 2:
1. Run both Phase 1 (fixed weights) and Phase 2 (regime weights) on the same data
2. Compare **out-of-sample** performance only — training performance doesn't count
3. Adopt Phase 2 only if it produces a measurably better score on the 3-month holdout set
4. If Phase 2 shows no improvement, keep Phase 1 and document the result here

---

## Step 6 — Assess and Implement

**Goal:** Use the Step 5 scoring data to select the best parameter set and promote it to production PSAR.

**Assessment criteria (in order of importance):**

1. Profit factor > 1.5 across most coin+timeframe combinations
2. Whipsaw rate < 20% — too many quick reversals makes the signal unreliable
3. Consistent across timeframes — a set that only works on 12h but fails on 1h is fragile
4. Consistent across coins — a set that only works on BTC is not generalizable
5. Consistent on recent data — historical winner must hold on last 30 days (out-of-sample)

**Decision outcomes:**

| Outcome | Condition | Action |
|---------|-----------|--------|
| Single winner | One set clearly wins on most metrics | Rename `psar_X_*` columns to `psar_value`, `psar_trend`, `psar_af`, `psar_ep`. Drop other 4 sets. |
| Timeframe-specific | Different sets win on different timeframes | Store winning set per timeframe in a config table. Signal engine selects parameters per timeframe. |
| No clear winner | No set achieves profit factor > 1.5 consistently | Return to Step 1 with a new parameter range. Do not implement until a winner is found. |

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
| `btcdatadb` has `timeframe` column | DONE |
| Data imported for all 6 timeframes | Partial — 12h done, others need import |
| `M_Coin_Data::updateBatch()` | DONE |
| PSAR batch calculation (Step 2) | DONE |
| Admin route + button infrastructure | DONE |
| ADX(14) + ATR(14) calculated (required for Phase 2) | NOT STARTED |

---

## Effort Estimate

| Step / Module | Effort |
|---------------|--------|
| Step 1 — Parameter sets definition | Done (in this doc) |
| Step 2 — PHP calculation function | Done |
| Step 3 — DB migration (20 columns) | 1–2 hours |
| Step 4 — Graphing + toggle UI | 4–6 hours |
| **Phase 1 — Metrics calculator** | 3–4 hours |
| **Phase 1 — Scoring function + normalization** | 2–3 hours |
| **Phase 1 — Aggregator/Reporter + train-test split** | 4–5 hours |
| **Phase 2 — Regime classifier** | 3–4 hours |
| **Phase 2 — Trade tagger + regime scoring + A/B gate** | 4–5 hours |
| Step 6 — Assessment + production implementation | 2–4 hours |
| **Total** | **~23–33 hours** |
