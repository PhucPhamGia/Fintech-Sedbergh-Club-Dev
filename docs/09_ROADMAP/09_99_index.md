# 09_ROADMAP — Index

**Last Updated:** 2026-04-11
**Scope:** Development roadmap & phase planning — CI4 Crypto Data Analysis Platform.

---

## Files

| File | Content | Status |
|------|---------|--------|
| [09_00_PROJECT_MASTER_ROADMAP_2026.md](09_00_PROJECT_MASTER_ROADMAP_2026.md) | Master roadmap 2026: 10 workstreams, Phase 0-8, PSAR-first indicator stack, dependency map, timeline | **ACTIVE** |
| [09_01_auto_trading_systems_reference.md](09_01_auto_trading_systems_reference.md) | Global auto trading systems survey: institutional firms, crypto bots, architecture patterns, risk management, PSAR strategies | **ACTIVE** |
| [09_02_multi_timeframe_architecture.md](09_02_multi_timeframe_architecture.md) | Multi-timeframe candle architecture: ALTER existing `btcdatadb` (add `timeframe` column + composite keys), 6 timeframes (15m→12h), import system, model update | **PROPOSED** |
| [09_03_psar_parameter_research.md](09_03_psar_parameter_research.md) | PSAR parameter research pipeline: 5 parameter sets, batch calculation, DB storage (20 columns), chart visualization, effectiveness comparison, production implementation | **PLANNED** |

---

## Reading Guide

**If your goal is to understand overall project direction:**
1. Start with `09_00_PROJECT_MASTER_ROADMAP_2026.md` — master roadmap, all phases & priorities
2. See `09_01_auto_trading_systems_reference.md` — how professional trading systems work worldwide

**If your goal is to understand what's already built:**
1. See `09_00` Section 7 (Current Status Snapshot)
2. Cross-reference with `00_ARCHITECTURE_FOUNDATION/00_04_project_overview_sectors.md`

**If your goal is to understand documentation standards:**
1. See `00_ARCHITECTURE_FOUNDATION/00_00_docs_storage_architecture_lawbook.md`
2. See `00_ARCHITECTURE_FOUNDATION/00_10_docs_normalization_master_plan_v1.md`

---

## Changelog

- r01 (2026-04-11): Created roadmap folder with master roadmap `09_00`.
- r02 (2026-04-11): Added `09_01` auto trading systems reference. Updated `09_00` with PSAR-first indicator strategy.
- r03 (2026-04-11): Added `09_02` multi-timeframe candle architecture. Updated `09_00` Signal + Filter architecture.
- r04 (2026-04-11): Simplified `09_02` to ALTER existing `btcdatadb` (3 ALTER statements) instead of creating new table. Updated `09_00` r05 to match.
- r05 (2026-04-19): Added `09_03` PSAR parameter research plan — 5 parameter sets (Conservative→Fast), 6-step pipeline from calculation to production implementation.
