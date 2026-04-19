# 07_01 — UI Sync Protocol: Admin vs Normal View

**Status:** ACTIVE
**Last Updated:** 2026-04-19
**Scope:** Rules for keeping `V_Database.php` and `admin/V_Database_Admin.php` in sync.

---

## The Problem

The project has two parallel database views:

| File | Served to |
|---|---|
| `app/Views/V_Database.php` | Normal (non-admin) users |
| `app/Views/admin/V_Database_Admin.php` | Admin users |

Both views share the same chart, table, coin widgets, search form, and CSS. When a change is made to one and not the other, the two UIs diverge — leading to bugs, visual inconsistencies, and wasted debugging time.

**Known incidents caused by this:**
- MA20 color change applied to normal view only — admin still showed yellow
- MA50 join logic fixed in normal view only — admin chart broken
- `inline style` override removed from normal view only — admin chart stayed fixed-size
- Stray `</div>` fixed in normal view only — admin had broken HTML
- `simple-btn` CSS class removed thinking it was unused — broke admin import buttons

---

## Rule: Always Edit Both Views Together

Any change to the chart, table, CSS classes, or shared UI elements **must be applied to both files simultaneously.**

### Shared components — always sync both:

| Component | Location in both files |
|---|---|
| Google Charts `drawChart()` function | `<script>` in `<head>` |
| Chart `options` object (colors, legend, series) | Inside `drawChart()` |
| `interpolateNulls`, `seriesType`, `candlestick` config | Inside `drawChart()` |
| CSS `<link>` tag and version param (`?v=N`) | Line 7 of `<head>` |
| Chart div (no inline styles) | `<div id="chart_div" class="chart-container">` |
| Coin widgets HTML | `.widget-container` block |
| Search form HTML | `.search-container` block |
| Data table HTML | `.table-container` block |
| Default `$fix` fallback value | `let currentFix = <?= $fix ?? 200 ?>` |
| Back button link | `<a href="<?= site_url('dashboard') ?>">` |

### Admin-only components — do NOT add to normal view:

| Component | File |
|---|---|
| Import/calculate buttons (`.simple-btn`) | `admin/V_Database_Admin.php` only |
| `simple-btn` CSS class | Must stay in `database.css` (used by admin) |

---

## Rule: CSS Changes Require Cache-Busting

The production server (LiteSpeed) caches static assets with `max-age=604800` (7 days). After any CSS change:

1. Increment `?v=N` in the `<link>` tag in **both** views:
   ```html
   <link rel="stylesheet" href="<?= base_url('assets/css/database.css') ?>?v=3">
   ```
2. Deploy both views to the server.

Current version: `?v=2`

---

## Rule: Default Candle Count

The default number of candles (`$fix`) is `200`. All of the following must use the same value:

| Location | File |
|---|---|
| `let currentFix = <?= $fix ?? 200 ?>` | Both views |
| `redirect()->to('database/1/200')` | `C_Database.php` (4 redirects) |
| `<a href="<?= site_url('database/1/200') ?>">Database</a>` | `V_Dashboard.php` |

---

## Deployment Checklist for View Changes

```
□ Change made in V_Database.php
□ Same change made in admin/V_Database_Admin.php
□ If CSS changed: incremented ?v=N in both views
□ Both views deployed via SCP:
    scp -P 2229 -i ~/.ssh/giaphuc_dev_ed25519 app/Views/V_Database.php ...
    scp -P 2229 -i ~/.ssh/giaphuc_dev_ed25519 app/Views/admin/V_Database_Admin.php ...
□ Committed to git
□ Verified on website (both admin and normal login)
```
