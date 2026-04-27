# 09_00 — Project Master Roadmap 2026 (CI4 Crypto Data Analysis)

**Created:** 2026-04-11
**Last Updated:** 2026-04-11 (r05 — Multi-timeframe via ALTER btcdatadb, not new table)
**Objective:** Comprehensive roadmap from MVP foundation to production-grade crypto analysis & automated trading platform.
**Status:** ACTIVE — Master plan consolidating all workstreams.
**Principle:** No automated trading with real funds until backtesting, paper trading, and risk controls are verified and stable.

---

## Quick Navigation

| # | Section | What's Inside | Jump To |
|---|---|---|---|
| 1 | Project Context | Tech stack, team, vision, timeline | [Section 1](#1-project-context) |
| 2 | Objectives | 6 project-level goals for 2026 | [Section 2](#2-project-level-objectives-2026) |
| 3 | Workstream Summary | 10 workstreams (WS1-WS10) with status and priority | [Section 3](#3-workstream-summary) |
| 4 | **Roadmap by Phase** | | |
| | Phase 0 — Foundation | COMPLETE. Framework, DB, API, auth, CI/CD | [Phase 0](#phase-0--foundation-baseline-complete) |
| | Phase 1 — Indicators | ACTIVE. Multi-TF ALTER of `btcdatadb` (1.0), PSAR signal + filters, 8 indicators, 23 indicator columns | [Phase 1](#phase-1--indicator-engine--chart-completion-active) |
| | Phase 2 — Data Automation | Per-timeframe cron schedule (15m→12h), auto-calc chain, data validation | [Phase 2](#phase-2--data-pipeline-automation--reliability-p1) |
| | Phase 3 — Admin Panel | Dashboard, user management, config UI | [Phase 3](#phase-3--admin-panel--management-tools-p1) |
| | Phase 4 — Trading Signals | Signal + Filter engine (Stage A/B/C), signal dashboard, alerts | [Phase 4](#phase-4--trading-signal-detection--alerts-p1) |
| | Phase 5 — Paper Trading | Trade simulation, performance analytics, backtesting | [Phase 5](#phase-5--paper-trading-simulator-p2) |
| | Phase 6 — Documentation | Doc normalization, testing framework, onboarding guide | [Phase 6](#phase-6--documentation--team-readiness-p1-ongoing) |
| | Phase 7 — Performance | DB optimization, caching, security audit | [Phase 7](#phase-7--performance--security-hardening-p2) |
| | Phase 8 — Advanced | Portfolio, multi-exchange, mobile, WebSocket, social | [Phase 8](#phase-8--advanced-features-p3-long-term) |
| 5 | Dependency Map | Phase dependency tree | [Section 5](#5-dependency-map) |
| 6 | Timeline | Rolling weekly schedule Apr-Jul 2026 | [Section 6](#6-suggested-timeline-rolling-adjusted-weekly) |
| 7 | Status Snapshot | What's DONE, what's next, technical debt | [Section 7](#7-current-status-snapshot-2026-04-11) |
| 8 | File Reference | Key files in the codebase | [Section 8](#8-file-reference) |
| 9 | Decision Gate | GO/NO-GO criteria before advancing phases | [Section 9](#9-decision-gate--go--no-go) |
| 10 | Changelog | Document revision history | [Section 10](#10-changelog) |

---

## 1) Project Context

**Project:** CI4 Crypto Data Analysis Platform
**URL:** https://giaphuc.thuytrieu.vn/public
**Vision:** Automatically analyze and trade cryptocurrency to generate consistent income while building developer skills and creating a portfolio project for university applications.

**Tech Stack:**
- Framework: CodeIgniter 4 (PHP 8.1+)
- Database: MySQL
- External API: Binance (kline/candlestick data)
- Frontend: HTML5 + Google Charts / Chart.js
- Deployment: GitHub Actions CI/CD → FTP

**Team:**
- **Project Lead:** Phuc (Architecture, decision-making, code review)
- **Development Team:** 4 junior developers (learning phase)
- **Started:** January 2026
- **Target MVP:** Q2 2026

---

## 2) Project-Level Objectives (2026)

1. Complete MVP: multi-timeframe crypto dashboard (15m, 30m, 1h, 3h, 6h, 12h) with technical indicators and candlestick charts for 4 coins (BTC, ETH, SOL, BNB).
2. Build a PSAR-first indicator engine using **Signal + Filter architecture** — PSAR as sole signal generator, ADX/RSI/OBV as binary gate filters, ATR for risk management. No percentage weights — this follows how professional systems (Freqtrade, institutional trend-followers) actually combine indicators.
3. Implement trading signal detection using cascading filter logic, evolving from binary gates → composite scoring → regime detection.
4. Build paper trading simulator to validate strategies before real execution.
5. Standardize operations: documentation, testing, deployment, code review practices for a 5-person team.
6. Prepare the platform as a portfolio project for university applications.

---

## 3) Workstream Summary

| Code | Workstream | Status | Priority |
|---|---|---|---|
| WS1 | Data Foundation (Binance Import + DB) | 12h import DONE. **Multi-TF via ALTER btcdatadb** — add `timeframe` column + composite keys, 6 TFs (15m→12h), `INSERT IGNORE` dedup | P0 |
| WS2 | Indicator Engine (PSAR signal + ADX/RSI/OBV filters + ATR risk) | MA20 DONE, MA50 backend DONE / frontend WIP, PSAR next | P0 |
| WS3 | Frontend Dashboard & Charts | Candlestick + MA20 chart DONE, MA50 line WIP, **timeframe selector planned** | P0 |
| WS4 | User Authentication & Authorization | **DONE** — Login, register, throttling, remember-me, roles | P0 |
| WS5 | Documentation Normalization | Lawbook DONE, Tier B/C partially created | P1 |
| WS6 | Admin Tools & Data Management | Basic import UI DONE, admin panel WIP | P1 |
| WS7 | Trading Signals & Alerts | NOT STARTED — Depends on WS2 indicators | P1 |
| WS8 | Paper Trading Simulator | NOT STARTED — Depends on WS7 signals | P2 |
| WS9 | Performance & Database Optimization | NOT STARTED — Index optimization, caching | P2 |
| WS10 | Advanced Features (Portfolio, Multi-exchange, Mobile) | NOT STARTED — Long-term | P3 |

---

## 4) Roadmap by Phase

## Phase 0 — Foundation Baseline (COMPLETE)

**Objective:** Establish the core infrastructure — framework, database, API integration, authentication.

**Deliverables (all DONE):**
1. CodeIgniter 4 project setup with proper directory structure.
2. MySQL database with 3 tables (`btcdatadb`, `tbl_coin`, `auth`).
3. Binance API integration — historical (100 days) and daily import at 12h intervals.
4. Duplicate prevention via composite key `(id_coin, date, open_time)`.
5. User authentication with login throttling (20 attempts/30min per IP).
6. GitHub Actions CI/CD pipeline for FTP deployment.
7. Documentation architecture (Lawbook, Tier A/B/C structure).

**Exit criteria:** All items verified and deployed to production. **PASSED.**

> **Note (r05):** Phase 0 data foundation will be extended in Phase 1 to multi-timeframe. The existing `btcdatadb` table will get a `timeframe` column + composite indexes via ALTER (no new table, no data migration). See [09_02_multi_timeframe_architecture.md](09_02_multi_timeframe_architecture.md) for full design.

---

## Phase 1 — Indicator Engine & Chart Completion (ACTIVE)

**Objective:** Build a PSAR-first indicator engine using **Signal + Filter architecture** — the same approach used by Freqtrade, institutional trend-following systems, and the majority of production trading systems worldwide.

**Architecture: Signal + Filter (No Percentage Weights)**

> **Why not percentage weights?** Professional systems don't assign static percentages to indicators. PSAR outputs a price level, RSI outputs 0-100, OBV outputs cumulative volume — you can't weight things on incomparable scales. Instead, real systems use one signal generator + binary gate filters. This is what Freqtrade does, what TradingView strategies do, and what the Turtle Trading rules pioneered.

**Indicator Roles (Signal + Filter Model):**

| Indicator | Role | Type | Logic | Priority |
|---|---|---|---|---|
| **PSAR** | **Sole signal generator** | Signal | PSAR flip = BUY/SELL. Only indicator that generates trade entries. | P0 |
| **ADX** | **Trend strength gate** | Filter (binary) | ADX > 25 → PASS (trending, trust PSAR). ADX < 20 → BLOCK (ranging, ignore PSAR). | P1 |
| **RSI-14** | **Overbought/oversold gate** | Filter (binary) | BUY: RSI < 70 → PASS. SELL: RSI > 30 → PASS. Otherwise → BLOCK. | P1 |
| **OBV** | **Volume confirmation gate** | Filter (binary) | OBV trending in signal direction → PASS. Diverging → BLOCK. | P1 |
| **ATR** | **Risk management** | Position sizing | `position_size = (equity × 0.02) / (ATR × 1.5)`. Not a signal or filter. | P1 |
| **Bollinger Bands** | **Squeeze detection** | Enhancement | Narrow bands + PSAR flip = high-confidence setup. | P2 |
| **MACD** | **Divergence warning** | Enhancement | MACD divergence = early warning of PSAR reversal. | P2 |
| **Stochastic RSI** | **Sensitive momentum** | Enhancement | More responsive than RSI in strong trends. | P3 |

**How signals flow (the actual architecture):**
```
PSAR flip detected? ──NO──→ HOLD (use psar_value as trailing stop)
        │
       YES
        │
ADX > 25? ────────NO──→ SKIP (ranging market, PSAR will whipsaw)
        │
       YES
        │
RSI in safe zone? ───NO──→ SKIP (overbought/oversold, bad entry)
        │
       YES
        │
OBV confirms? ───────NO──→ REDUCED CONFIDENCE (still tradeable, smaller size)
        │
       YES
        │
Calculate position size using ATR
        │
   → EXECUTE TRADE
```

**Evolution plan (3 phases of signal sophistication):**

| Stage | Architecture | When | What Changes |
|---|---|---|---|
| **Stage A** | Signal + Filter (binary AND gates) | Phase 4.1 (first implementation) | PSAR signal, ADX/RSI/OBV as yes/no gates |
| **Stage B** | Composite Scoring (integer points) | After backtesting Stage A | Each filter contributes +1/+2 points, trade at score >= threshold |
| **Stage C** | Regime Detection (top-level switch) | After validating Stage B | ADX determines market regime → different strategy per regime |

**Deliverables:**

### 1.0 Multi-Timeframe Data Extension — NOT STARTED (P0) ⭐ FOUNDATION UPGRADE
- **What:** Extend existing `btcdatadb` to support 6 timeframes (15m, 30m, 1h, 3h, 6h, 12h) via 3 ALTER statements. No new table.
- **Why:** Multi-timeframe analysis is essential for accurate trading signals. A PSAR flip on 12h confirmed by 1h trend alignment is far more reliable than 12h alone.
- **Architecture:** ALTER existing table. Existing 1,736 rows auto-tagged `timeframe='12h'` via column DEFAULT. See [09_02_multi_timeframe_architecture.md](09_02_multi_timeframe_architecture.md) for full design.
- **Key changes:**

  | Component | Current | New |
  |---|---|---|
  | Table | `btcdatadb` (no timeframe col) | `btcdatadb` + `timeframe` column + composite keys |
  | Dedup | PHP `countAllResults()` per row | `UNIQUE KEY (id_coin, timeframe, open_time)` + `INSERT IGNORE` |
  | Import | 2 hardcoded methods, 12h only | 1 generic method, timeframe as parameter, auto-pagination |
  | Model | `M_Coin_Data` queries all rows | `M_Coin_Data` + timeframe param on methods + `insertBatchIgnore()` |
  | Controller | Import logic mixed in `C_Database` | New `C_Import` controller (separated concern) |
  | Routes | `/database/{coin}/{days}` | `/database/{coin}/{timeframe}/{days}` |
  | Data volume | 8 rows/day, ~2,920/year | 728 rows/day, ~265,720/year (~50 MB, trivial) |

- **The 3 ALTER statements:**
  ```sql
  -- Change 1: Add timeframe column (existing rows auto-tagged '12h' via DEFAULT)
  ALTER TABLE btcdatadb 
    ADD COLUMN timeframe VARCHAR(4) NOT NULL DEFAULT '12h' AFTER id_coin;
  
  -- Change 2: Drop weak single-column index
  ALTER TABLE btcdatadb DROP INDEX open_time;
  
  -- Change 3: Add composite indexes (unique key prevents duplicates, enables INSERT IGNORE)
  ALTER TABLE btcdatadb 
    ADD UNIQUE KEY uq_coin_tf_time (id_coin, timeframe, open_time);
  ALTER TABLE btcdatadb 
    ADD KEY idx_coin_tf_date (id_coin, timeframe, date);
  ```
- **Migration steps:**
  1. Run the 3 ALTER statements (CI4 migration, phpMyAdmin, or SSH)
  2. Update `M_Coin_Data` — add `'timeframe'` to `$allowedFields`, add `insertBatchIgnore()`, `getCandles($coinId, $tf)`, `countByTimeframe()`
  3. Create `C_Import` controller with generic import method + reusable cURL wrapper
  4. Run `/import/historical` for 15m, 30m, 1h, 3h, 6h, 12h (100 days each) — INSERT IGNORE skips existing 12h duplicates
  5. Update `C_View::Database()` and routes to accept timeframe parameter
  6. Add timeframe selector buttons to `V_Database.php`
  7. Verify data with `M_Coin_Data::countByTimeframe()`
- **Files:** CI4 migration, update `M_Coin_Data.php`, new `C_Import.php`, update `C_View.php`, `V_Database.php`, `Routes.php`
- **Effort:** ~6-8 hours (reduced from 8-10 — no data migration needed)
- **Dependency:** None — can start immediately
- **Risk:** Very low — existing 12h data untouched, rollback is reverting the 3 ALTER statements

### 1.1 MA50 Frontend Rendering (WIP — P0)
- **Status:** Backend calculation DONE, frontend rendering IN PROGRESS
- **What remains:**
  1. Update `V_Database.php` to render MA50 line on Google Charts ComboChart
  2. Add legend toggle for MA20/MA50 visibility
  3. Color differentiation: MA20 = blue, MA50 = orange
  4. Test on all 4 coins with sufficient data (>100 candles)
- **Files:** `app/Views/V_Database.php`, `app/Controllers/C_View.php`
- **Effort:** ~2-3 hours
- **Risk:** Low — data already available, only chart rendering needed

### 1.2 PSAR (Parabolic SAR) — NOT STARTED (P0) ⭐ SOLE SIGNAL GENERATOR
- **What:** Parabolic Stop and Reverse — plots dots above/below price for trend direction + trailing stop
- **Role:** The ONLY indicator that generates trade signals. All other indicators are filters or risk tools.
- **Why PSAR for crypto:** Crypto trends hard on 12h candles. PSAR captures multi-day trends, provides built-in trailing stop-loss at every candle, and gives clear unambiguous signals (dot flips = signal). Self-adjusting via acceleration factor.
- **Formula (recursive):**
  ```
  UPTREND: SAR[i] = SAR[i-1] + AF * (EP - SAR[i-1])
  DOWNTREND: SAR[i] = SAR[i-1] + AF * (EP - SAR[i-1])
  
  AF: starts at 0.02, increments by 0.02 when EP updates, max 0.20
  EP: highest high (uptrend) or lowest low (downtrend)
  
  REVERSAL: when price crosses SAR → flip trend, SAR = EP, AF = 0.02
  CLAMP: SAR cannot exceed prior 2 lows (uptrend) / highs (downtrend)
  ```
- **Parameters (12h crypto recommended):**
  | Param | Value | Notes |
  |---|---|---|
  | AF_START | 0.02 | Default. Lower = too slow, higher = more whipsaws |
  | AF_INCREMENT | 0.02 | Controls acceleration speed |
  | AF_MAX | 0.20 | Max tightness. Test 0.16-0.22 for volatile coins (SOL/BNB) |
- **Signals:**
  | Event | Signal |
  |---|---|
  | PSAR flips below price (was above) | **BUY** — trend reversal to bullish |
  | PSAR flips above price (was below) | **SELL** — trend reversal to bearish |
  | PSAR stays below price | HOLD LONG — uptrend continues |
  | PSAR stays above price | STAY OUT — downtrend continues |
- **Strengths:** Clear signals, built-in trailing stop, self-adjusting, low computation O(n)
- **Weakness:** Whipsaw in ranging markets → solved by ADX filter (section 1.3)
- **DB Columns:**
  ```sql
  psar_value  DECIMAL(18,8) -- SAR dot value (plotted on chart, used as stop-loss)
  psar_trend  TINYINT(1)    -- 1 = uptrend (dots below), 0 = downtrend (dots above)
  psar_af     DECIMAL(6,4)  -- current acceleration factor (for incremental calc)
  psar_ep     DECIMAL(18,8) -- current extreme point (for incremental calc)
  ```
- **Requirements:**
  1. Add 4 columns to `btcdatadb` via migration
  2. Create `C_Database::PSAR()` with sliding window calculation
  3. Store `psar_af` and `psar_ep` for incremental updates (avoid full recalculation)
  4. Render PSAR dots on candlestick chart (green dots below = uptrend, red dots above = downtrend)
  5. Add PSAR flip detection for signal generation (Phase 4)
- **Files:** `app/Controllers/C_Database.php`, `app/Views/V_Database.php`, DB migration
- **Effort:** ~6-8 hours
- **Dependency:** Phase 0 complete
- **Chart rendering:** Dots overlay on candlestick (same panel as MA lines)

### 1.3 ADX + ATR (Trend Strength + Volatility) — NOT STARTED (P1)
- **What:** ADX measures trend STRENGTH (0-100). ATR measures volatility (average price range per candle). Both created by J. Welles Wilder (same as PSAR).
- **Role:** ADX = binary gate filter (pass/block PSAR signals). ATR = position sizing tool (not a signal or filter).
- **Why ADX is critical for PSAR:** PSAR's #1 weakness is whipsaw in sideways markets. ADX directly solves this:
  - ADX > 25 + PSAR flip = **TRUST the signal** (trending market)
  - ADX < 20 + PSAR flip = **IGNORE the signal** (likely whipsaw)
- **Why ATR matters:** Position sizing (risk 1 ATR per trade), stop-loss validation (PSAR stop < 0.5 ATR = too tight), volatility spike detection
- **Formula:**
  ```
  -- Directional Movement
  +DM = max(high[i] - high[i-1], 0) if > -DM, else 0
  -DM = max(low[i-1] - low[i], 0)   if > +DM, else 0
  
  -- True Range
  TR = max(high-low, abs(high-prev_close), abs(low-prev_close))
  
  -- 14-period Wilder smoothing
  ATR14 = Wilder_smooth(TR, 14)
  +DI = (+DM14 / ATR14) * 100
  -DI = (-DM14 / ATR14) * 100
  
  -- ADX
  DX = abs(+DI - -DI) / (+DI + -DI) * 100
  ADX = Wilder_smooth(DX, 14)
  
  -- Wilder smoothing: smooth[i] = smooth[i-1] - (smooth[i-1]/N) + value[i]
  ```
- **DB Columns:**
  ```sql
  adx_14       DECIMAL(8,4)  -- ADX value (0-100)
  plus_di_14   DECIMAL(8,4)  -- +DI value
  minus_di_14  DECIMAL(8,4)  -- -DI value
  atr_14       DECIMAL(18,8) -- ATR value (same scale as price)
  ```
- **Requirements:**
  1. Add 4 columns to `btcdatadb` via migration
  2. Create `C_Database::ADX_ATR()` — calculates both together (ATR is intermediate step of ADX)
  3. Render ADX as separate subplot (horizontal line at 25 = threshold)
  4. ATR used internally for position sizing (Phase 5), optional chart display
- **Files:** `app/Controllers/C_Database.php`, `app/Views/V_Database.php`, DB migration
- **Effort:** ~6-8 hours
- **Dependency:** Phase 0 complete

### 1.4 RSI-14 (Momentum) — NOT STARTED (P1)
- **What:** 14-period Relative Strength Index for overbought/oversold detection
- **Role:** Binary gate filter — blocks PSAR BUY if overbought (>70), blocks PSAR SELL if oversold (<30). Also useful for divergence early-warning.
- **Why with PSAR:** PSAR BUY + RSI < 70 → PASS (safe to enter). PSAR BUY + RSI > 70 → BLOCK (overbought, bad entry). RSI divergence warns PSAR flip is coming.
- **Formula:** `RSI = 100 - (100 / (1 + RS))` where `RS = avg_gain / avg_loss` over 14 periods (Wilder smoothing)
- **DB Columns:**
  ```sql
  rsi_14  DECIMAL(8,4) -- RSI value (0-100)
  ```
- **Requirements:**
  1. Add `rsi_14` column to `btcdatadb`
  2. Create `C_Database::RSI14()`
  3. Render as subplot: overbought zone (>70, red), oversold zone (<30, green)
- **Files:** `app/Controllers/C_Database.php`, `app/Views/V_Database.php`, DB migration
- **Effort:** ~4-6 hours
- **Dependency:** Phase 0 complete

### 1.5 OBV (On-Balance Volume) — NOT STARTED (P1)
- **What:** Cumulative volume indicator — adds volume on up-candles, subtracts on down-candles
- **Role:** Binary gate filter — confirms PSAR signal has volume backing. OBV trending in signal direction → PASS. Diverging → BLOCK or reduce position size.
- **Why with PSAR:** PSAR bullish flip + OBV rising = volume supports the move → PASS. PSAR flip + OBV flat/declining = weak move → BLOCK. OBV divergence is a leading indicator.
- **Formula:**
  ```
  If close[i] > close[i-1]: OBV[i] = OBV[i-1] + volume[i]
  If close[i] < close[i-1]: OBV[i] = OBV[i-1] - volume[i]
  If close[i] = close[i-1]: OBV[i] = OBV[i-1]
  ```
- **DB Columns:**
  ```sql
  obv  DECIMAL(30,8) -- OBV can be very large for BTC (cumulative volume)
  ```
- **Requirements:**
  1. Add `obv` column to `btcdatadb`
  2. Create `C_Database::OBV()`
  3. Render as separate subplot with trend line
- **Files:** `app/Controllers/C_Database.php`, `app/Views/V_Database.php`, DB migration
- **Effort:** ~2-3 hours
- **Dependency:** Phase 0 complete

### 1.6 Bollinger Bands — NOT STARTED (P2)
- **What:** 20-period SMA ± 2 standard deviations — volatility envelope
- **Why with PSAR:** Bollinger squeeze (bands narrow) → breakout imminent. PSAR flip during/after squeeze = high-probability signal. Price at upper band + PSAR bullish = consider partial profit.
- **Formula:** `Upper = MA20 + 2×StdDev(close,20)`, `Lower = MA20 - 2×StdDev`, `Width = (Upper-Lower)/MA20`
- **DB Columns:**
  ```sql
  bb_upper  DECIMAL(18,8) -- Upper band
  bb_lower  DECIMAL(18,8) -- Lower band
  bb_width  DECIMAL(10,6) -- Normalized bandwidth for squeeze detection
  ```
- **Requirements:**
  1. Add 3 columns to `btcdatadb`
  2. Create `C_Database::BollingerBands()`
  3. Render as shaded area overlay on candlestick chart
- **Files:** `app/Controllers/C_Database.php`, `app/Views/V_Database.php`, DB migration
- **Effort:** ~4-6 hours
- **Dependency:** MA20 calculation working (DONE)

### 1.7 MACD — NOT STARTED (P2)
- **What:** Moving Average Convergence Divergence — trend momentum
- **Why with PSAR:** MACD divergence detection + histogram momentum confirmation. Less critical when PSAR is primary (both are trend-following), but useful for convergence signals.
- **Formula:** `MACD = EMA12 - EMA26`, `Signal = EMA9(MACD)`, `Histogram = MACD - Signal`
- **DB Columns:**
  ```sql
  ema_12          DECIMAL(18,8)
  ema_26          DECIMAL(18,8)
  macd_line       DECIMAL(18,8)
  macd_signal     DECIMAL(18,8)
  macd_histogram  DECIMAL(18,8)
  ```
- **Requirements:**
  1. Add 5 columns to `btcdatadb`
  2. Create `C_Database::MACD()`
  3. Render as subplot with histogram bars + signal line
- **Effort:** ~6-8 hours
- **Dependency:** Phase 0 complete

### 1.8 Stochastic RSI — NOT STARTED (P3)
- **What:** Stochastic oscillator applied to RSI values — faster, more sensitive than standard RSI
- **Why for crypto:** Standard RSI can sit at 75 for weeks during BTC bull runs. Stochastic RSI oscillates faster, better detecting short-term overbought/oversold within trending markets.
- **Formula:** `StochRSI = (RSI - min(RSI,14)) / (max(RSI,14) - min(RSI,14))`, `K = SMA(StochRSI,3)`, `D = SMA(K,3)`
- **DB Columns:**
  ```sql
  stoch_rsi_k  DECIMAL(8,4)
  stoch_rsi_d  DECIMAL(8,4)
  ```
- **Requirements:**
  1. Add 2 columns to `btcdatadb`
  2. Create `C_Database::StochRSI()` (depends on RSI being calculated first)
  3. Render as subplot with K/D crossover lines
- **Effort:** ~3-4 hours
- **Dependency:** RSI-14 (1.4) must be implemented first

### DB Migration Summary (All Indicator Columns on `btcdatadb`)

> **Note:** All ALTER TABLE statements target the existing `btcdatadb` (extended in Phase 1.0).

```sql
-- Phase 1.2: PSAR
ALTER TABLE btcdatadb ADD COLUMN psar_value DECIMAL(18,8) DEFAULT NULL;
ALTER TABLE btcdatadb ADD COLUMN psar_trend TINYINT(1) DEFAULT NULL;
ALTER TABLE btcdatadb ADD COLUMN psar_af DECIMAL(6,4) DEFAULT NULL;
ALTER TABLE btcdatadb ADD COLUMN psar_ep DECIMAL(18,8) DEFAULT NULL;

-- Phase 1.3: ADX + ATR
ALTER TABLE btcdatadb ADD COLUMN adx_14 DECIMAL(8,4) DEFAULT NULL;
ALTER TABLE btcdatadb ADD COLUMN plus_di_14 DECIMAL(8,4) DEFAULT NULL;
ALTER TABLE btcdatadb ADD COLUMN minus_di_14 DECIMAL(8,4) DEFAULT NULL;
ALTER TABLE btcdatadb ADD COLUMN atr_14 DECIMAL(18,8) DEFAULT NULL;

-- Phase 1.4: RSI
ALTER TABLE btcdatadb ADD COLUMN rsi_14 DECIMAL(8,4) DEFAULT NULL;

-- Phase 1.5: OBV
ALTER TABLE btcdatadb ADD COLUMN obv DECIMAL(30,8) DEFAULT NULL;

-- Phase 1.6: Bollinger Bands
ALTER TABLE btcdatadb ADD COLUMN bb_upper DECIMAL(18,8) DEFAULT NULL;
ALTER TABLE btcdatadb ADD COLUMN bb_lower DECIMAL(18,8) DEFAULT NULL;
ALTER TABLE btcdatadb ADD COLUMN bb_width DECIMAL(10,6) DEFAULT NULL;

-- Phase 1.7: MACD
ALTER TABLE btcdatadb ADD COLUMN ema_12 DECIMAL(18,8) DEFAULT NULL;
ALTER TABLE btcdatadb ADD COLUMN ema_26 DECIMAL(18,8) DEFAULT NULL;
ALTER TABLE btcdatadb ADD COLUMN macd_line DECIMAL(18,8) DEFAULT NULL;
ALTER TABLE btcdatadb ADD COLUMN macd_signal DECIMAL(18,8) DEFAULT NULL;
ALTER TABLE btcdatadb ADD COLUMN macd_histogram DECIMAL(18,8) DEFAULT NULL;

-- Phase 1.8: Stochastic RSI
ALTER TABLE btcdatadb ADD COLUMN stoch_rsi_k DECIMAL(8,4) DEFAULT NULL;
ALTER TABLE btcdatadb ADD COLUMN stoch_rsi_d DECIMAL(8,4) DEFAULT NULL;

-- Also carry over from btcdatadb:
-- ma20 DOUBLE DEFAULT NULL  (included in btcdatadb CREATE TABLE or via ALTER)
-- ma50 DOUBLE DEFAULT NULL
```

**Total new columns:** 21 indicators + 2 existing MAs = 23 indicator columns on `btcdatadb`

### Signal Architecture Preview (Phase 4 Implementation)

**Stage A — Binary Gate Logic (first implementation):**
```php
// PHP pseudocode for SignalDetector.php
function evaluate(array $candle, array $prev_candle): string
{
    // Step 1: Signal generator — ONLY PSAR generates signals
    $psar_flipped = ($candle['psar_trend'] !== $prev_candle['psar_trend']);
    if (!$psar_flipped) {
        return 'HOLD';  // no flip = no signal, use psar_value as trailing stop
    }
    
    $direction = ($candle['psar_trend'] == 1) ? 'BUY' : 'SELL';
    
    // Step 2: Filters — each is binary YES/NO, ALL must pass
    // Filter 1: ADX (trending market?)
    if ($candle['adx_14'] < 25) {
        return 'SKIP';  // ranging market, PSAR will whipsaw
    }
    
    // Filter 2: RSI (not overbought/oversold?)
    if ($direction == 'BUY' && $candle['rsi_14'] > 70) {
        return 'SKIP';  // overbought, bad entry
    }
    if ($direction == 'SELL' && $candle['rsi_14'] < 30) {
        return 'SKIP';  // oversold, bad entry
    }
    
    // Filter 3: OBV (volume confirms direction?)
    $obv_rising = ($candle['obv'] > $prev_5_candles_avg_obv);
    if ($direction == 'BUY' && !$obv_rising) {
        return 'SKIP';  // no volume support
    }
    
    // Step 3: Position sizing — ATR-based (not a filter, risk management)
    $stop_distance = abs($candle['close_price'] - $candle['psar_value']);
    $position_size = ($equity * 0.02) / $stop_distance;  // risk 2% per trade
    
    // Validate stop isn't too tight
    if ($stop_distance < $candle['atr_14'] * 0.5) {
        $stop_distance = $candle['atr_14'] * 1.5;  // use ATR-based stop instead
    }
    
    return $direction;  // BUY or SELL — all gates passed
}
```

**Stage B — Composite Scoring (evolution after backtesting Stage A):**
```php
// Instead of binary pass/fail, each filter contributes points
$score = 0;
if ($psar_flipped)                    $score += 2;  // required, but worth more points
if ($candle['adx_14'] > 25)           $score += 1;  // trending
if ($candle['adx_14'] > 40)           $score += 1;  // strongly trending (bonus)
if ($rsi_in_safe_zone)                $score += 1;
if ($obv_confirms)                    $score += 1;
if ($bb_squeeze_breakout)             $score += 1;  // Bollinger squeeze + PSAR flip

// score >= 5 → full position, >= 3 → half position, < 3 → no trade
```

**Stage C — Regime Detection (future, after Stage B validated):**
```php
// Top-level switch changes which strategy runs
$regime = detect_regime($candle['adx_14'], $candle['atr_14']);

switch ($regime) {
    case 'TRENDING':   // ADX > 25 → trust PSAR signals
        return evaluate_psar_with_filters($candle);
    case 'RANGING':    // ADX < 20 → ignore PSAR, use RSI mean-reversion
        return evaluate_rsi_mean_reversion($candle);
    case 'VOLATILE':   // ATR spike > 2x normal → reduce position sizes
        return evaluate_psar_with_filters($candle, reduced_size: true);
}
```

**Exit criteria:** All indicators calculate correctly, render on charts with proper colors/legends, work for all 4 coins. PSAR is the primary chart overlay with dots. ADX/RSI/OBV/MACD render as subplots.

---

## Phase 2 — Data Pipeline Automation & Reliability (P1)

**Objective:** Automate data collection so indicators stay current without manual intervention.

**Deliverables:**

### 2.1 Multi-Timeframe Cron Schedule — NOT STARTED (P0)
- **What:** Automate imports for all 6 timeframes via per-timeframe cron jobs using `C_Import`
- **Cron schedule:**
  ```
  */15 * * * *  curl -s ".../import/cron?tf=15m&key=SECRET"   # every 15 min
  */30 * * * *  curl -s ".../import/cron?tf=30m&key=SECRET"   # every 30 min
  0 * * * *     curl -s ".../import/cron?tf=1h&key=SECRET"    # every hour
  0 */3 * * *   curl -s ".../import/cron?tf=3h&key=SECRET"    # every 3 hours
  0 */6 * * *   curl -s ".../import/cron?tf=6h&key=SECRET"    # every 6 hours
  0 0,12 * * *  curl -s ".../import/cron?tf=12h&key=SECRET"   # every 12 hours
  ```
- **API call budget:** 24 calls/daily-cycle (4 coins × 6 TFs). Binance limit: 1,200/min. No concern.
- **Requirements:**
  1. API key authentication on import endpoints (`SECRET` in `.env`, checked in controller)
  2. Import logging (coin, timeframe, inserted count, timestamp, duration)
  3. Error notification (email or log alert on failure)
- **Effort:** ~4-5 hours
- **Dependency:** Phase 1.0 (multi-timeframe migration) complete

### 2.2 Auto-Calculate Indicators Per Timeframe — NOT STARTED (P1)
- **What:** After each timeframe's cron import, recalculate indicators for that timeframe only
- **Flow per timeframe:**
  ```
  import(tf) → MA20(tf) → MA50(tf) → PSAR(tf) → ADX_ATR(tf) → RSI14(tf)
             → OBV(tf) → BollingerBands(tf) → MACD(tf) → StochRSI(tf)
             → SignalDetector(tf) → log result
  ```
- **Optimization:** Only recalculate the timeframe that just received new data, not all 6 TFs
- **Requirements:**
  1. All indicator methods accept `timeframe` parameter (query `btcdatadb WHERE timeframe = ?`)
  2. Add execution time tracking per indicator per timeframe
  3. Handle partial failures (one indicator fails, others still run)
- **Effort:** ~3-4 hours
- **Dependency:** WS2 indicators implemented + Phase 1.0 migration

### 2.3 Data Validation & Health Check — NOT STARTED (P2)
- **What:** Detect and alert on data quality issues across all timeframes
- **Checks per timeframe:**
  | Timeframe | Expected Candles/Day | Alert If Missing |
  |---|---|---|
  | 15m | 96 | < 90 |
  | 30m | 48 | < 45 |
  | 1h | 24 | < 22 |
  | 3h | 8 | < 7 |
  | 6h | 4 | < 3 |
  | 12h | 2 | < 2 |
- **Additional checks:**
  - Stale data (no update within 2× timeframe interval)
  - Price anomalies (>20% change in single candle)
  - Cross-timeframe consistency (12h close should ≈ latest 1h close)
  - API connectivity issues
- **Implementation:** Use `M_Coin_Data::countByTimeframe()` method from new model
- **Effort:** ~4-5 hours

**Exit criteria:** All 6 timeframes import automatically, indicators recalculate per-timeframe, data quality monitored across all TFs.

---

## Phase 3 — Admin Panel & Management Tools (P1)

**Objective:** Build admin interface for managing data imports, viewing system health, and configuring the platform.

**Deliverables:**

### 3.1 Admin Dashboard — PARTIAL (P1)
- **Status:** Basic `V_Database_Admin.php` exists with import buttons
- **What remains:**
  1. System status overview (last import time per timeframe, record counts, data freshness)
  2. Multi-timeframe data health matrix (coins × timeframes → row counts, gaps, last update)
  3. Import history log with success/fail indicators per timeframe
  4. Manual trigger buttons for import + indicator calculation (per timeframe)
  5. Database statistics (rows per coin per timeframe, date range coverage)
- **Effort:** ~8-10 hours

### 3.2 User Management — NOT STARTED (P2)
- **What:** Admin CRUD for user accounts
- **Requirements:**
  1. List all users with roles, last login, verified status
  2. Enable/disable accounts
  3. Reset passwords
  4. Assign roles (admin/user)
- **Effort:** ~4-6 hours

### 3.3 System Configuration UI — NOT STARTED (P2)
- **What:** Web-based configuration instead of editing `.env` or database directly
- **Settings:** API keys, import intervals, indicator parameters, alert thresholds
- **Effort:** ~4-5 hours

**Exit criteria:** Admin can manage imports, users, and system settings entirely through the web UI.

---

## Phase 4 — Trading Signal Detection & Alerts (P1)

**Objective:** Analyze indicator data to generate buy/sell signals and alert the user.

**Deliverables:**

### 4.1 Signal Detection Engine — NOT STARTED (P1)
- **What:** Detect trading signals using Signal + Filter architecture (see Phase 1 architecture preview)
- **Signal generator (PSAR only):**
  - PSAR flip below price → **BUY** signal candidate
  - PSAR flip above price → **SELL** signal candidate
  - PSAR value → **trailing stop-loss** at every candle
- **Binary gate filters (all must PASS for signal to execute):**
  - ADX gate: ADX > 25 → PASS (trending) | ADX < 20 → BLOCK (ranging, whipsaw risk)
  - RSI gate: BUY + RSI < 70 → PASS | BUY + RSI > 70 → BLOCK (overbought)
  - OBV gate: volume trending in signal direction → PASS | diverging → BLOCK
  - ATR validation: PSAR stop distance > 0.5 ATR → PASS | too tight → widen to 1.5 ATR
- **Enhancement signals (bonus confidence, not required):**
  - Bollinger Squeeze: bands narrow + PSAR flip = high-confidence setup
  - MACD alignment: MACD histogram in same direction as PSAR = extra confirmation
  - MA Cross: MA20 × MA50 in signal direction = trend alignment
- **Implementation stages:**
  1. **Stage A (first):** Binary AND gates — all filters must pass (see Phase 1 architecture preview)
  2. **Stage B (after backtesting):** Composite scoring — filters contribute integer points, trade at threshold
  3. **Stage C (future):** Regime detection — ADX determines market type → different strategy per regime
- **Requirements:**
  1. Create `app/Helpers/SignalDetector.php` with Stage A logic
  2. Create `p_signals` table (timestamp, coin, signal_type, direction, price, psar_value, adx_value, rsi_value, obv_trend, filters_passed, filters_total)
  3. Run signal detection after each indicator update
  4. Log every signal AND every rejected signal (for debugging and backtesting)
- **Files:** New helper, new model, new DB table, update cron chain
- **Effort:** ~10-12 hours
- **Dependency:** Phase 1 indicators complete (minimum: PSAR + ADX + RSI)

### 4.2 Signal Dashboard — NOT STARTED (P1)
- **What:** Display detected signals on the chart and in a dedicated panel
- **Requirements:**
  1. Signal markers on candlestick chart (arrows/icons at signal points)
  2. Signal history table with filters (coin, type, date range)
  3. Real-time signal count badges in navigation
- **Effort:** ~6-8 hours

### 4.3 Alert System — NOT STARTED (P2)
- **What:** Notify user when signals are detected
- **Channels:**
  - Email notification (using CI4 Email library)
  - Telegram bot (optional, via Telegram Bot API)
  - In-app notification banner
- **Effort:** ~4-6 hours

**Exit criteria:** System detects signals from indicators, displays them on charts, and can optionally notify the user.

---

## Phase 5 — Paper Trading Simulator (P2)

**Objective:** Simulate trading based on signals to validate strategies before risking real money.

**Deliverables:**

### 5.1 Trade Simulation Engine — NOT STARTED (P2)
- **What:** Execute virtual trades based on signal rules
- **Requirements:**
  1. Virtual portfolio with starting balance (default: $10,000 USDT)
  2. Execute buy/sell at signal price
  3. Track position: entry price, quantity, unrealized P&L
  4. Apply basic fees (0.1% per trade, matching Binance)
  5. Store trades in `p_paper_trades` table
- **Effort:** ~8-10 hours

### 5.2 Performance Analytics — NOT STARTED (P2)
- **What:** Measure strategy effectiveness
- **Metrics:**
  - Win rate (% of profitable trades)
  - Profit factor (gross profit / gross loss)
  - Max drawdown
  - Average trade P&L
  - Sharpe ratio (if enough data)
- **Effort:** ~6-8 hours

### 5.3 PSAR Parameter Optimization + Backtest Engine — NOT STARTED (P3)
- **What:** Evaluate 5 PSAR parameter sets across 4 coins × 6 timeframes to determine optimal parameters per combination, then generalize the backtest engine to other strategies.
- **Full design:** See [`09_03_psar_parameter_research.md`](09_03_psar_parameter_research.md) — Steps 5–6 plus the two backtesting phases below.
- **Phase 1 — Fixed-Weight Evaluation (build first):**
  1. Metrics calculator: PnL %, Win Rate, MDD %, Profit Factor, Trade Count
  2. Scoring function: `Score = 0.35×ProfitFactor + 0.30×(1/MDD) + 0.20×PnL + 0.15×WinRate` (each metric normalized 0–10)
  3. Aggregator: full matrix [5 sets × 4 coins × 6 timeframes], outputs winner per coin/timeframe
  4. Validation: 9-month training / 3-month out-of-sample split; minimum 30 trades per backtest required
- **Phase 2 — Regime-Aware Refinement (after Phase 1 validated):**
  1. Regime classifier using ADX(14) + ATR%(14): VOLATILE / TRENDING / SIDEWAYS with 3-candle confirmation
  2. Trade-regime tagging: each trade labeled with its entry regime
  3. Regime-specific weights and A/B gate: adopt only if Phase 2 beats Phase 1 out-of-sample
- **Effort:** Phase 1 ~9-12 hours, Phase 2 ~7-9 hours
- **Dependency:** Phase 1.2 (PSAR batch done) + Phase 1.3 (ADX/ATR, required for Phase 2)

**Exit criteria:** Can simulate trading for any strategy, measure performance, and backtest against historical data.

---

## Phase 6 — Documentation & Team Readiness (P1, Ongoing)

**Objective:** Complete documentation normalization and establish team development practices.

**Deliverables:**

### 6.1 Documentation Normalization — IN PROGRESS (P1)
- **Status:** Lawbook DONE, Tier A partially created, Tier B/C mostly empty
- **What remains (from Normalization Master Plan v1):**
  1. Create all `*_99_index.md` files in each folder
  2. Extract documentation from `C_Database.php` code comments → Engine docs
  3. Create API contract docs (`02_API_CONTRACTS/`)
  4. Create DB schema docs in markdown format
  5. Populate engine-specific docs (Binance API, Data Processing, Indicators)
- **Effort:** ~8-10 hours total
- **Reference:** `docs/00_ARCHITECTURE_FOUNDATION/00_10_docs_normalization_master_plan_v1.md`

### 6.2 Testing Framework — NOT STARTED (P2)
- **What:** Set up PHPUnit tests for critical paths
- **Priority tests:**
  1. MA20/MA50 calculation correctness (known input → expected output)
  2. Duplicate prevention logic
  3. API response parsing
  4. Authentication flow (login/logout/throttling)
- **Effort:** ~6-8 hours

### 6.3 Team Onboarding Guide — NOT STARTED (P2)
- **What:** Documentation for new developers joining the team
- **Contents:**
  1. Local development setup guide
  2. Code review checklist
  3. Git branching strategy
  4. Deployment process
  5. Architecture overview for newcomers
- **Effort:** ~4-5 hours

**Exit criteria:** All documentation follows Lawbook standards, critical paths have tests, new developers can onboard independently.

---

## Phase 7 — Performance & Security Hardening (P2)

**Objective:** Optimize database queries, add caching, and harden security.

**Deliverables:**

### 7.1 Database Optimization — NOT STARTED (P2)
- **What:** Add indexes and optimize slow queries
- **Tasks:**
  1. Add composite index on `btcdatadb(id_coin, open_time)` for faster lookups
  2. Add index on `btcdatadb(id_coin, date)` for date-range queries
  3. Profile query execution times with `EXPLAIN`
  4. Consider partitioning `btcdatadb` by coin if data grows large
- **Effort:** ~3-4 hours

### 7.2 Caching Layer — NOT STARTED (P2)
- **What:** Cache frequently accessed data to reduce DB load
- **Candidates:**
  - Coin list (changes rarely)
  - Latest price per coin (update every 12h)
  - Indicator values for dashboard (update after recalculation)
- **Implementation:** CI4 Cache library (file-based or Redis if available)
- **Effort:** ~3-4 hours

### 7.3 Security Audit — NOT STARTED (P2)
- **What:** Review and harden security
- **Tasks:**
  1. Input validation audit on all controllers
  2. SQL injection prevention verification
  3. CSRF token enforcement on all forms
  4. API endpoint authentication (prevent unauthorized imports)
  5. Rate limiting on public endpoints
  6. Secret management (`.env` not in git, no hardcoded keys)
- **Effort:** ~4-6 hours

**Exit criteria:** Queries optimized with proper indexes, caching reduces page load times, security audit passes with no critical findings.

---

## Phase 8 — Advanced Features (P3, Long-term)

**Objective:** Extend the platform with advanced capabilities after core is stable.

**Deliverables (future backlog):**

### 8.1 User Portfolio Tracking
- Personal watchlist and portfolio value tracking
- P&L history and performance charts

### 8.2 Multi-Exchange Support
- Add support for other exchanges (Coinbase, Kraken, OKX)
- Unified data model across exchanges

### 8.3 Mobile Responsive / PWA
- Optimize all views for mobile devices
- Consider Progressive Web App for push notifications

### 8.4 Real-time Price Updates
- WebSocket connection to Binance for live price ticks
- Replace 60-second polling on home page

### 8.5 Social / Sharing Features
- Share charts with annotations
- Public signal leaderboard

**No timeline set — these are post-MVP features dependent on Phase 1-5 completion.**

---

## 5) Dependency Map

```
Phase 0 (Foundation) ← DONE
  ├── Phase 1 (Indicators & Charts) ← ACTIVE
  │     ├── Phase 2 (Data Automation) ← after cron + indicators
  │     └── Phase 4 (Signal Detection) ← after indicators complete
  │           └── Phase 5 (Paper Trading) ← after signals working
  ├── Phase 3 (Admin Panel) ← can start now, no blockers
  ├── Phase 6 (Documentation) ← ongoing, no blockers
  └── Phase 7 (Performance) ← after Phase 1-3 stable

Phase 8 (Advanced) ← after Phase 1-5 complete
```

---

## 6) Suggested Timeline (Rolling, adjusted weekly)

**Note:** Timeline is directional, not a commitment. Safety and quality override speed.

| Period | Focus | Key Deliverables |
|---|---|---|
| Apr 2026 (W2-W3) | Phase 1.0 + 1.1 + 6.1 | **Multi-timeframe migration** (`btcdatadb`, `M_Coin_Data`, `C_Import`), MA50 frontend, doc normalization |
| Apr 2026 (W4) - May (W1) | Phase 1.2 + 1.3 | **PSAR implementation** (sole signal generator), **ADX+ATR** (trend filter + risk) |
| May 2026 (W1-W2) | Phase 1.4 + 1.5 + 2.1 | RSI-14, OBV, **multi-timeframe cron schedule** (15m→12h) |
| May 2026 (W3-W4) | Phase 2.2 + 3.1 | Per-timeframe auto-calc chain, admin dashboard with TF health matrix |
| Jun 2026 (W1-W2) | Phase 1.6 + 1.7 + 4.1 | Bollinger Bands, MACD, **Signal + Filter engine (Stage A: binary gates)** |
| Jun 2026 (W3-W4) | Phase 1.8 + 4.2 + 4.3 | Stochastic RSI, signal dashboard + alert system |
| Jul 2026 (W1) | Phase 7.1 + 7.3 | DB optimization (composite indexes on `btcdatadb`) + security audit |
| Jul 2026+ | Phase 5 + Phase 8 | Paper trading, backtesting, advanced features |

---

## 7) Current Status Snapshot (2026-04-11)

### What's DONE
1. **Phase 0 COMPLETE:** Framework, DB, API, auth, CI/CD, documentation architecture.
2. **WS1 Data Foundation:** Binance import (historical + daily) for BTC, ETH, SOL, BNB.
3. **WS2 Partial:** MA20 fully working (backend + frontend). MA50 backend complete.
4. **WS3 Partial:** Candlestick charts with MA20 overlay. Home page with live prices.
5. **WS4 COMPLETE:** User auth with throttling, roles, remember-me, email verification.
6. **WS5 Partial:** Lawbook finalized, Tier A/B/C folder structure created, content partially filled.

### What's Next (Immediate Priority)
1. **[P0]** Multi-timeframe extension — 3 ALTER statements on `btcdatadb`, update `M_Coin_Data`, new `C_Import` controller, import 15m/30m/1h/3h/6h historical data
2. **[P0]** Complete MA50 frontend rendering on `V_Database.php` + add timeframe selector
3. **[P0]** Implement PSAR — sole signal generator, backend + chart dots overlay (on `btcdatadb`)
4. **[P1]** Implement ADX+ATR — ADX = binary gate filter for PSAR, ATR = position sizing tool
5. **[P1]** Implement RSI-14 + OBV — binary gate filters (overbought/oversold + volume confirmation)
6. **[P1]** Set up multi-timeframe cron schedule (15m→12h) + per-TF indicator auto-calc chain
7. **[P1]** Continue documentation normalization (index files, engine docs)

### Known Technical Debt
1. ~~Import endpoints lack API key authentication~~ → **Addressed in Phase 1.0/2.1** — `C_Import` will require API key via `.env`
2. ~~No database indexes beyond primary key~~ → **Addressed in Phase 1.0** — `btcdatadb` has composite unique key + date index
3. No automated tests (PHPUnit configured but no test cases written)
4. ~~`C_Database.php` mixing import logic + indicator logic~~ → **Addressed in Phase 1.0** — import logic moves to `C_Import`, indicators stay in `C_Database`
5. Google Charts used for candlestick — may need to migrate to Chart.js for consistency
6. Raw cURL used for Binance API — `C_Import` uses reusable `callBinanceApi()` wrapper; consider migrating to **CCXT-PHP** (`ccxt/ccxt` via Composer) for multi-exchange support later
7. Consider implementing **SuperTrend** alongside raw PSAR — ATR-based PSAR derivative, cleaner signals (see `09_01_auto_trading_systems_reference.md`)
8. Old `btcdatadb` table to be dropped after 30-day transition period (Phase 1.0 migration)

---

## 8) File Reference

| File | Purpose |
|---|---|
| `docs/00_ARCHITECTURE_FOUNDATION/00_00_docs_storage_architecture_lawbook.md` | Documentation naming & structure laws (LOCKED) |
| `docs/00_ARCHITECTURE_FOUNDATION/00_04_project_overview_sectors.md` | Project sectors & implementation status |
| `docs/00_ARCHITECTURE_FOUNDATION/00_10_docs_normalization_master_plan_v1.md` | Documentation migration plan |
| `.copilot-instructions.md` | AI coding guidelines & schema reference |
| `docs/07_OPS_DEVOPS/07_00_deployment_guide.md` | Deployment guide |
| `docs/09_ROADMAP/09_01_auto_trading_systems_reference.md` | Global auto trading systems survey & best practices |
| `docs/09_ROADMAP/09_02_multi_timeframe_architecture.md` | Multi-timeframe candle architecture (full design) |
| `docs/09_ROADMAP/09_03_psar_parameter_research.md` | PSAR parameter research pipeline + backtesting system (Phase 1 fixed-weight, Phase 2 regime-aware) |
| `app/Controllers/C_Database.php` | Indicator logic (to be refactored — import moves to C_Import) |
| `app/Controllers/C_Import.php` | **NEW** — Multi-timeframe import controller |
| `app/Models/M_Coin_Data.php` | UPDATED — Add `'timeframe'` to `$allowedFields` + new timeframe-aware methods |
| `app/Controllers/C_Auth.php` | Authentication (426 lines) |
| `app/Controllers/C_View.php` | View rendering (139 lines) |
| `app/Views/V_Database.php` | Candlestick chart view (242 lines) |
| `app/Views/V_Home.php` | Landing page with live prices |

---

## 9) Decision Gate (GO / NO-GO)

Before advancing to any phase that involves real trading or external system integration:

1. **All prerequisite phase deliverables are verified working.**
2. **Automated tests exist for critical calculation logic (indicators, signals).**
3. **Data validation confirms no gaps or anomalies in the dataset.**
4. **Documentation is up to date for the components being used.**

Missing any condition → **NO-GO** for that phase. Fix first, then proceed.

---

## 10) Changelog

- r01 (2026-04-11): Created master roadmap. Consolidated project state from codebase analysis, existing documentation (Lawbook, Project Overview, Normalization Plan), and git history. Defined 8 phases, 10 workstreams, and rolling timeline through Q3 2026.
- r02 (2026-04-11): Added PSAR as primary indicator, ADX, RSI, OBV, ATR, Bollinger Bands, MACD, Stochastic RSI. Added DB migration summary (21 new columns).
- r03 (2026-04-11): **Architecture correction** — Removed percentage weights (60/15/10/10/5). Replaced with **Signal + Filter architecture**: PSAR as sole signal generator, ADX/RSI/OBV as binary gate filters, ATR for position sizing. Added 3-stage evolution plan. Updated Phase 4, timeline, and status snapshot.
- r04 (2026-04-11): **Multi-timeframe architecture** (initial) — Proposed new `kline_data` table with 6 timeframes. New model, controller, per-timeframe cron.
- r05 (2026-04-11): **Simplified to ALTER existing `btcdatadb`** — No new table. Just 3 ALTER statements (add `timeframe` column + composite unique/date indexes). Existing 1,736 rows auto-tagged `timeframe='12h'` via column DEFAULT. Existing `M_Coin_Data` model extended (not replaced). Reduced Phase 1.0 effort from 8-10h to 6-8h. Rollback is trivial. All indicator ALTER statements still target `btcdatadb`.
- r06 (2026-04-27): **PSAR Parameter Optimization System added** — Expanded Phase 5.3 from a generic backtest stub to a full two-phase scoring system. Phase 1: fixed-weight metrics (ProfitFactor/MDD/PnL/WinRate) with 9-month train / 3-month out-of-sample validation and a 30-trade minimum gate. Phase 2: regime-aware refinement using ADX+ATR classification, adopted only if it beats Phase 1 out-of-sample. Full design lives in `09_03_psar_parameter_research.md`.
