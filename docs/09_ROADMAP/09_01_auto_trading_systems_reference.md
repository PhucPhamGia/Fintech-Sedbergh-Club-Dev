# 09_01 — Auto Trading Systems Reference (Global Landscape)

**Created:** 2026-04-11
**Status:** ACTIVE — Reference document for architecture decisions
**Scope:** Survey of automated trading systems used by companies worldwide, mapped to Featherlight (our CI4 PSAR-first platform)
**Audience:** Development team — for understanding industry context and best practices

---

## 1. Major Institutional Algo Trading Firms

### 1.1 Key Players

| Firm | Type | Known Strategies | Tech Stack |
|---|---|---|---|
| **Renaissance Technologies** (Medallion Fund) | Quant Hedge Fund | Statistical arbitrage, pattern recognition, mean reversion | C++, proprietary math models. ~66% annual return (before fees) for decades |
| **Two Sigma** | Quant Hedge Fund | Machine learning, alternative data (satellite, sentiment) | Python, Spark, Hadoop, Kafka, AWS/GCP. ~$60B AUM |
| **Citadel / Citadel Securities** | Hedge Fund + Market Maker | Market making, stat arb, high-frequency | C++, FPGA, co-location. Handles ~25% of US equity volume |
| **DE Shaw** | Quant Hedge Fund | Systematic macro, statistical arbitrage | C++, Python, proprietary risk systems. ~$60B AUM |
| **Jump Trading / Jump Crypto** | Prop Trading + Crypto | HF market making, DeFi, cross-exchange arbitrage | C++, FPGA, active in Solana/Wormhole ecosystem |
| **Jane Street** | Prop Trading | ETF arbitrage, options market making, crypto OTC | OCaml (entire stack), functional programming focus |
| **Virtu Financial** | Market Maker | HF market making across equities, FX, crypto | C++, co-location. Profitable 1,237 out of 1,238 trading days |
| **Wintermute** | Crypto Market Maker | Market making across 50+ crypto exchanges, DeFi | Custom C++/Python, CEX + on-chain execution |
| **GSR Markets** | Crypto Market Maker | OTC, algorithmic execution | Active since 2013, one of oldest crypto-native market makers |

> **Cautionary tale:** Alameda Research (FTX) — collapsed 2022 due to risk management failures and fraud. Technically sophisticated but fatally flawed risk controls.

### 1.2 Institutional Strategy Categories

| Strategy | Description | Timeframe | Relevance to Our Project |
|---|---|---|---|
| **Trend Following** | Riding momentum using MAs, breakouts, PSAR | Days to months | **HIGH** — core of our PSAR-first approach |
| **Mean Reversion** | Betting price returns to a mean after deviation | Hours to days | **HIGH** — RSI + Bollinger bands |
| **Momentum/Breakout** | Entering on key level breaks with volume confirmation | Hours to days | **HIGH** — PSAR is a breakout/trend indicator |
| **Statistical Arbitrage** | Exploiting mean-reverting relationships between correlated assets | Minutes to days | MEDIUM — pair trading BTC/ETH possible on 12h |
| **Market Making** | Placing bid/ask to earn spread | Milliseconds | LOW — requires co-location and deep capital |
| **Cross-Exchange Arbitrage** | Buy on one exchange, sell on another | Seconds | LOW — latency-sensitive, PHP not ideal |
| **Funding Rate Arbitrage** | Long spot + short perpetual for funding payments | Hours to days | MEDIUM — possible with Binance futures API later |

### 1.3 Institutional Tech Stacks

| Purpose | Languages/Tools |
|---|---|
| Execution / Low-Latency | C++, Rust, FPGA (Verilog/VHDL) |
| Research / Backtesting | Python (NumPy, Pandas, SciPy, scikit-learn, PyTorch) |
| Data Pipelines | Kafka, Redis, TimescaleDB, InfluxDB, Apache Flink |
| Infrastructure | Kubernetes, Docker, Prometheus/Grafana |
| Time-series DB | kdb+/q (banks), PostgreSQL, ClickHouse |

**Where PHP/CI4 fits:** PHP is not used by any major trading firm for core execution. However, for a **dashboard, monitoring UI, signal visualization, and manual override**, CI4 is perfectly reasonable. On **12h candles**, microsecond latency is irrelevant — PHP execution speed is more than sufficient for signal generation and order placement.

---

## 2. Crypto-Specific Auto Trading Platforms

### 2.1 Commercial Bot Platforms

| Platform | Type | Key Features | Pricing |
|---|---|---|---|
| **3Commas** | SaaS Bot | DCA bots, Grid bots, SmartTrade terminal, signal marketplace, copy trading | $29-$99/mo |
| **Pionex** | Exchange + Bots | 16 free built-in bots (Grid, DCA, rebalancing), aggregates Binance liquidity | Free (0.05% trading fee) |
| **Cryptohopper** | SaaS Bot | AI trading, strategy designer, backtesting, marketplace, paper trading | $24-$108/mo |
| **Bitsgap** | SaaS Bot | Grid bots (GRID, DCA, BTD, COMBO), arbitrage scanner, portfolio tracker | $28-$143/mo |
| **Coinrule** | Rule-Based Bot | "If-This-Then-That" builder, 150+ templates, no coding | Free tier + $30-$450/mo |
| **Shrimpy** | Portfolio Mgmt | Portfolio rebalancing, social trading, index fund creation | Subscription |
| **Hummingbot** | Open-Source MM | Professional market making, liquidity mining, custom Python strategies | Free |

### 2.2 Open-Source Crypto Trading Frameworks

| Framework | Language | Status | Key Features | Stars |
|---|---|---|---|---|
| **Freqtrade** | Python | **Active, mature** | Full strategy framework, backtesting, hyperopt, Telegram bot, web UI, dry-run | ~28k |
| **CCXT** | JS/Python/**PHP** | **Active, essential** | Unified API for 100+ exchanges. **Supports PHP — usable in CI4** | ~33k |
| **Jesse** | Python | **Active** | Research-focused, clean API, advanced backtesting, live trading | ~6k |
| **Hummingbot** | Python | **Active** | Market making focus, connector framework, DEX gateway | ~8k |
| **OctoBot** | Python | **Active** | Modular, AI evaluators, social trading, web interface | ~3k |
| **Gekko** | Node.js | Deprecated (2018) | Was popular, plugin architecture. **Abandoned.** | ~10k |
| **Zenbot** | Node.js | Deprecated (2020) | CLI-based, genetic algo optimization. **Abandoned.** | ~8k |

> **Key insight: CCXT has a PHP library** (`ccxt/ccxt` via Composer). This means Binance API calls can be made through a well-tested, community-maintained abstraction layer instead of raw cURL. This is the single most relevant open-source tool for our CI4 project.

### 2.3 Bot Strategy Types in the Market

| Strategy | How It Works | Best Market | Risk |
|---|---|---|---|
| **DCA Bot** | Buys at regular intervals or on dips, averages entry | Ranging / slowly declining (long-term bullish) | Low-Medium |
| **Grid Bot** | Buy/sell orders at fixed intervals in a range, profits from oscillation | **Sideways** markets | Medium |
| **Signal Bot** | Executes trades based on indicator signals (TradingView webhooks, PSAR flips) | Depends on signal quality | Variable |
| **Arbitrage Bot** | Exploits price differences between exchanges | Any | Low (needs speed) |
| **Futures/Leverage Bot** | DCA or Grid on futures with leverage | Trending | **High** |
| **Copy Trading Bot** | Mirrors trades of profitable trader | Depends on leader | Variable |
| **Rebalancing Bot** | Periodically rebalances to target allocations | Long-term holding | Low |

### 2.4 Signal Architecture Used by Commercial Platforms

Most platforms use a **webhook/signal model:**

```
TradingView (PineScript) → Indicator triggers (PSAR flip, RSI cross)
    ↓ HTTP webhook POST (JSON payload)
Bot Platform (3Commas, Cryptohopper) → Parse signal
    ↓ Exchange API call
Binance → Execute order
```

**This is directly replicable in CI4:** Create a controller endpoint that receives webhook POSTs, validates the signal, and triggers order execution via CCXT-PHP/Binance API.

---

## 3. Standard Auto Trading System Architecture

### 3.1 Universal Components (Every Serious System Has These)

```
┌──────────────────────────────────────────────────────────┐
│                    DATA LAYER                             │
│  Market Data Feed → Normalization → Storage (TimeSeries) │
│  (WebSocket/REST)   (OHLCV, ticks)  (DB or in-memory)   │
└──────────────────────┬───────────────────────────────────┘
                       │
┌──────────────────────▼───────────────────────────────────┐
│                  SIGNAL LAYER                             │
│  Indicator Calc → Signal Generation → Confidence Score   │
│  (PSAR, RSI, ADX)  (BUY/SELL/HOLD)   (60-95% based on   │
│                                        filter alignment)  │
└──────────────────────┬───────────────────────────────────┘
                       │
┌──────────────────────▼───────────────────────────────────┐
│               RISK MANAGEMENT LAYER                      │
│  Position Sizing → Stop-Loss/TP → Max Drawdown Check     │
│  (1-2% risk, ATR)  (PSAR trailing) (Portfolio level)     │
└──────────────────────┬───────────────────────────────────┘
                       │
┌──────────────────────▼───────────────────────────────────┐
│               EXECUTION LAYER                            │
│  Order Routing → Order Type Selection → Retry/Error      │
│  (CCXT → Binance) (Limit/Market/Stop)  (Exp. backoff)   │
└──────────────────────┬───────────────────────────────────┘
                       │
┌──────────────────────▼───────────────────────────────────┐
│               MONITORING LAYER                           │
│  Trade Logging → P&L Tracking → Alerts → Dashboard       │
│  (MySQL)         (Real-time)    (Email/   (CI4 Views)    │
│                                  Telegram)                │
└──────────────────────────────────────────────────────────┘
```

### 3.2 Mapping to Our CI4 Architecture

| Component | Our Implementation |
|---|---|
| **Data Layer** | Cron (every 12h) → Binance API via cURL/CCXT-PHP → OHLCV → MySQL `btcdatadb` |
| **Signal Layer** | PHP service: `PSAR()` → `ADX_ATR()` → `RSI14()` → `OBV()` → `SignalDetector` |
| **Risk Management** | PHP service: position sizing (ATR-based), max drawdown check, kill switch |
| **Execution** | PHP: send order to Binance via CCXT-PHP. Prefer limit orders (lower fees) |
| **Monitoring** | CI4 dashboard: `V_Dashboard.php` showing trade history, P&L, signals, positions |

### 3.3 Professional Approaches to Key Concerns

**Backtesting:**
- Replay historical candles through signal engine as if live
- **Walk-forward optimization:** optimize on period A, test on period B, slide forward
- Avoid **overfitting:** if strategy needs >5 parameters, it's probably overfit
- Track: total return, Sharpe ratio, max drawdown, win rate, profit factor
- **Our approach:** Build a backtesting controller that reads historical candles from DB and simulates trades without API calls

**Paper Trading (Dry Run):**
- Identical to live but orders are simulated, not sent to exchange
- Freqtrade calls this "dry-run mode" — real market data, fake balances
- **Our approach:** Add `mode` config (live/paper). Paper mode logs to `paper_trades` table

**Position Sizing:**
| Method | Formula | When to Use |
|---|---|---|
| **Fixed Fraction** | Risk X% per trade (1-2%) | **Recommended** — simple, robust |
| **Kelly Criterion** | f = (win_prob × avg_win - loss_prob × avg_loss) / avg_win | Advanced — use "half-Kelly" for safety |
| **Volatility-based** | Size inversely proportional to ATR | Good complement to PSAR trailing stop |

---

## 4. PSAR in Production Systems

### 4.1 Indicators Used by Professional Crypto Traders

| Indicator | Category | Relevance on 12h Candles |
|---|---|---|
| **Parabolic SAR** | Trend / Stop-Loss | **Good** — 12h reduces whipsaws vs lower timeframes |
| **ADX (14)** | Trend Strength | **Excellent** — essential PSAR filter |
| **RSI (14)** | Momentum | **Excellent** — very reliable on higher TFs |
| **ATR (14)** | Volatility | **Essential** — position sizing + stop validation |
| **OBV** | Volume | **Good** — confirms PSAR signals with volume flow |
| **SuperTrend** | Trend (PSAR derivative) | **Excellent** — cleaner signals than raw PSAR |
| **EMA (20, 50, 200)** | Trend | **Excellent** — dynamic support/resistance |
| **Bollinger Bands** | Volatility | **Good** — squeeze detection for entry timing |
| **MACD** | Trend Momentum | **Good** — divergence detection |
| **Ichimoku Cloud** | Multi-purpose | **Good** on 12h+ — trend, S/R, momentum all-in-one |
| **Stochastic RSI** | Momentum | Medium — more sensitive than RSI, but can whipsaw |

### 4.2 Proven PSAR + Filter Combinations

| Combination | Logic | Why It Works |
|---|---|---|
| **PSAR + ADX** | Only take PSAR signals when ADX > 25 | Filters ranging markets where PSAR whipsaws |
| **PSAR + EMA(50)** | Only long above EMA, short below | Aligns with major trend direction |
| **PSAR + RSI** | PSAR buy + RSI < 70; PSAR sell + RSI > 30 | Avoids buying tops and selling bottoms |
| **PSAR + Volume (OBV)** | Only take signal when volume > 20-period average | Confirms genuine breakouts |
| **PSAR + MACD** | PSAR buy + MACD histogram positive | Double trend confirmation |
| **PSAR + Bollinger** | PSAR reversal near band extremes | High-probability mean reversion entries |

### 4.3 SuperTrend — The PSAR Evolution

SuperTrend is often described as a "cleaned up" PSAR. Widely used by crypto algorithmic traders.

**Formula:**
```
Basic Upper Band = (High + Low) / 2 + Multiplier × ATR(period)
Basic Lower Band = (High + Low) / 2 - Multiplier × ATR(period)

Default: period = 10, multiplier = 3
Crypto-tuned: (10, 2) or (7, 3)

If close > previous SuperTrend → SuperTrend = Lower Band (uptrend)
If close < previous SuperTrend → SuperTrend = Upper Band (downtrend)
```

**Advantages over raw PSAR:**
- ATR-based → adapts to volatility automatically
- Fewer whipsaws in ranging markets
- Simpler: one line that flips between support and resistance
- Only needs 2 parameters vs PSAR's 3

**DB columns needed:**
```sql
supertrend_value  DECIMAL(18,8)  -- SuperTrend line value
supertrend_trend  TINYINT(1)     -- 1 = uptrend, 0 = downtrend
```

> **Recommendation:** Consider implementing SuperTrend alongside raw PSAR. On 12h candles for BTC/ETH/SOL/BNB, SuperTrend (10, 3) may generate cleaner signals. Can run both and compare in backtesting.

### 4.4 Proven PSAR-Based Strategies for 12h Crypto

**Strategy 1: PSAR Trend Filter (Primary)**
```
Entry Long:   PSAR flips below price AND ADX > 25 AND price > MA50
Entry Short:  PSAR flips above price AND ADX > 25 AND price < MA50
Stop-Loss:    PSAR dot (trails automatically)
Take-Profit:  2× ATR(14) from entry, or when PSAR reverses
```

**Strategy 2: PSAR + RSI Mean Reversion**
```
Entry Long:   PSAR flips below price AND RSI(14) < 40 (recovering from oversold)
Entry Short:  PSAR flips above price AND RSI(14) > 60 (declining from overbought)
Stop-Loss:    1.5× ATR(14)
Take-Profit:  When RSI reaches opposite extreme or PSAR reverses
```

**Strategy 3: SuperTrend + MACD Confirmation**
```
Entry Long:   SuperTrend flips bullish AND MACD histogram > 0
Entry Short:  SuperTrend flips bearish AND MACD histogram < 0
Stop-Loss:    SuperTrend line (dynamic trailing stop)
Take-Profit:  When either indicator flips
```

---

## 5. Risk Management Systems

### 5.1 Per-Trade Risk Controls

| Control | Implementation | Typical Values (Crypto) |
|---|---|---|
| **Stop-Loss** | Hard price level — close at loss | 1-3% of portfolio, or 1-2× ATR |
| **Take-Profit** | Hard price level — take profit | 2-6% of portfolio, or 2-3× ATR (min 2:1 reward:risk) |
| **Trailing Stop** | Stop follows price in favorable direction | PSAR dot = built-in trailing stop |
| **Time-based Stop** | Close if no profit after N candles | Close after 10-20 candles (5-10 days on 12h) |
| **Break-even Stop** | Move stop to entry after reaching 1R profit | After 1× risk in profit → stop = entry |

### 5.2 Portfolio-Level Risk Controls

| Control | Description | Example |
|---|---|---|
| **Max Open Positions** | Limit concurrent trades | Max 4 positions across BTC/ETH/SOL/BNB |
| **Max Portfolio Risk** | Total risk across all positions | Never risk > 6% simultaneously |
| **Max Drawdown** | Stop trading if portfolio drops X% from peak | Halt if drawdown > 15% |
| **Daily Loss Limit** | Max loss per day | If daily loss > 3%, no new trades |
| **Correlation Limit** | Avoid correlated exposure | BTC/ETH/SOL/BNB are 70-90% correlated — treat as one allocation |
| **Max Per Asset** | Don't overweight one coin | Max 30% in any single asset |

### 5.3 System-Level Safety

**Kill Switch:**
- Single flag in DB that halts ALL trading immediately
- Every execution method checks this flag before sending orders
- Prominent red button on CI4 dashboard
- CI4: `settings` table with `kill_switch` boolean

**Circuit Breaker (automatic kill switch):**
- 5+ consecutive API errors → halt
- Price moves > X% in one candle (flash crash) → halt
- Account balance drops below minimum → halt
- Unusual spread or liquidity → halt

**Pre-flight Checks (before every order):**
```
1. Kill switch off?
2. Asset tradeable? (Exchange status)
3. Sufficient balance?
4. Within position size limits?
5. Within max open positions?
6. Within portfolio-level risk?
7. Price within reasonable range? (fat finger protection)
8. Signal not stale?
```

### 5.4 Crypto-Specific Risks

- **Exchange risk:** Never keep all capital on one exchange (FTX collapse lesson)
- **API rate limits:** Binance limits vary by endpoint — implement exponential backoff
- **Correlation risk:** BTC/ETH/SOL/BNB move together 70-90% in major moves — NOT truly diversified
- **Slippage on 12h:** Minimal concern (not scalping), but prefer limit orders
- **API key security:** Store encrypted, IP whitelisting on Binance, read+trade only (never withdrawal)

---

## 6. Lessons & Best Practices

### 6.1 Common Mistakes

| Mistake | How to Avoid |
|---|---|
| **Overfitting** | Out-of-sample testing. If strategy needs >5 parameters, probably overfit |
| **No risk management** | Build risk management FIRST, strategy second |
| **Look-ahead bias** | Indicators only use data available at signal time |
| **Ignoring fees** | Binance spot: 0.1% taker. Include in all backtests |
| **Ignoring slippage** | Add 0.05-0.1% slippage in backtests |
| **Over-trading** | 12h candles naturally limit this (good choice) |
| **No logging** | Log EVERY decision: signal generated, risk check pass/fail, order, fill |
| **No paper trading** | Run paper for at least 1-2 months before live |
| **Emotional override** | Trust system or fix it. Don't half-automate |
| **Single point of failure** | Use exchange-side stop-losses (OCO orders) so stops work if server dies |

### 6.2 What Separates Successful Systems from Failures

**Successful:**
- **Positive expectancy** verified over 200+ backtested trades
- **Simple, robust** strategies (2-3 indicators max)
- **Comprehensive risk management** limiting drawdowns
- **Excellent logging and monitoring**
- **Proper position sizing** — no single trade can damage portfolio significantly
- **Patient** — 12h candles = 2-5 trades/month/asset. That's fine.

**Failed:**
- Over-optimized to historical data (curve fitting)
- No stop-losses or inadequate risk controls
- Too many indicators creating conflicting signals
- Built for one market regime only (e.g., only bull markets)
- No monitoring — operators don't notice failures
- Inadequate error handling — API failures cause missed exits

### 6.3 PHP/CI4 Specific Best Practices

- **Use CCXT-PHP** (`ccxt/ccxt` via Composer) for exchange connectivity — handles Binance quirks, auth, rate limiting
- **Cron execution:** CI4 CLI commands (`spark`) triggered by system cron for the 12h cycle
- **Database:** CI4 migrations for schema. Proper indexes on `(id_coin, open_time)`
- **Configuration:** `.env` for API keys and trading params. Never commit secrets
- **Logging:** CI4 Logger with dedicated `trading` channel
- **Error handling:** Wrap all Binance API calls in try/catch, log failures, exponential backoff
- **Queue system:** Consider database-backed job queue to decouple signals from execution

---

## 7. Signal Combination Architecture

### Why NOT Percentage Weights

Professional systems do NOT use static percentage weights (like "60% PSAR, 15% ADX"). This is a common misconception. Reasons:
- Indicators output incomparable scales (PSAR = price level, RSI = 0-100, OBV = cumulative volume)
- Relative importance changes with market conditions
- No known institutional system, hedge fund, or serious open-source bot uses this approach
- Freqtrade, TradingView, Turtle Trading — all use binary logic, not weighted averages

### What Professional Systems Actually Use

| Architecture | How It Works | Who Uses It | Our Plan |
|---|---|---|---|
| **Signal + Filter** | One indicator generates signals, others are YES/NO gates | Freqtrade, TradingView, most retail algos | **Stage A (first)** |
| **Composite Scoring** | Indicators contribute integer points, trade at threshold | IBD ratings, some quant screeners | **Stage B (after backtesting)** |
| **Regime Detection** | Top-level switch (trending/ranging/volatile) selects strategy | Institutional systematic funds | **Stage C (future)** |
| **ML Dynamic Weights** | ML model learns non-linear combinations | Renaissance, Two Sigma | NOT suitable (too few data points on 12h, no ML infra in PHP) |
| **Multi-Strategy Portfolio** | Independent strategies with capital allocation | Hedge funds (AQR, Man Group) | Year 2+ if needed |

### Our Architecture: Signal + Filter

```
PSAR flip? ──NO──→ HOLD
     │YES
ADX > 25? ──NO──→ SKIP (ranging market)
     │YES
RSI safe? ──NO──→ SKIP (overbought/oversold)
     │YES
OBV confirms? ──NO──→ REDUCED SIZE
     │YES
ATR position sizing → EXECUTE
```

**The real sophistication is in risk management, not signal generation.** Turtle Trading's edge was position sizing and drawdown limits, not clever indicator combinations.

## 8. Summary: Key Decisions for Our Platform

| Decision | Recommendation | Rationale |
|---|---|---|
| Signal architecture | **Signal + Filter (binary gates)** | How Freqtrade, TradingView, and institutional systems actually work |
| Signal generator | **PSAR** (sole signal source, consider SuperTrend alongside) | Trend-following, built-in trailing stop, suits 12h candles |
| Gate filters | **ADX + RSI + OBV** (binary pass/block) | ADX filters ranging, RSI prevents bad entries, OBV confirms volume |
| Position sizing | **ATR-based, 1-2% risk per trade** | `size = (equity × 0.02) / (ATR × 1.5)` — professional standard |
| Stop-loss | PSAR dot or 1.5× ATR | Dynamic, adapts to volatility |
| Exchange library | **CCXT-PHP** (Composer) | Battle-tested, supports Binance, works with PHP/CI4 |
| Data storage | MySQL with proper indexing | CI4 native, sufficient for 12h candle volumes |
| Backtesting | Custom PHP engine | Educational, integrated with existing codebase |
| Paper trading | Minimum 2 months before live | Validates strategy before risking real money |
| Max open positions | 2-4 across all pairs | BTC/ETH/SOL/BNB highly correlated |
| Kill switch | DB flag + dashboard button | Essential safety mechanism |
| Evolution path | Gates → Scoring → Regime detection | Each stage independently useful and deployable |

---

## Liên kết

- [Project Master Roadmap](09_00_PROJECT_MASTER_ROADMAP_2026.md)
- [Indicator Engine Docs](../01_ENGINE_DOMAIN/03_INDICATOR_ENGINE/01_03_01_ma20_calculation_sector.md)
- [Project Overview](../00_ARCHITECTURE_FOUNDATION/00_04_project_overview_sectors.md)
