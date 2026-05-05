# AGENTS.md — FinTech GiaPhuc

> This file is intended for AI coding agents. It assumes zero prior knowledge of the project. All information below is derived from the actual codebase — do not guess or generalize beyond what is documented here.

---

## Project Overview

**FinTech GiaPhuc** is a cryptocurrency data analysis platform built on **CodeIgniter 4** (PHP 8.1+) with a **MySQL** backend. It fetches OHLCV kline (candlestick) data from the **Binance API** across six timeframes (`15m`, `30m`, `1h`, `4h`, `6h`, `12h`), stores it in a local database, calculates technical indicators (MA20, MA50, Parabolic SAR), and renders interactive charts. The long-term goal is an automated trading engine.

- **Live site**: `https://giaphuc.thuytrieu.vn/`
- **Framework**: CodeIgniter 4 (PHP 8.1+)
- **Database**: MySQL (`thuy_giaphuc`)
- **Front-end**: Vanilla HTML/CSS/JS with canvas animations; no JS framework
- **Language**: All code, comments, and documentation are in **English**

---

## Technology Stack

| Layer | Technology |
|-------|-----------|
| Language | PHP 8.1+ |
| Framework | CodeIgniter 4 |
| Database | MySQL (production), SQLite3 (PHPUnit tests) |
| HTTP Client | cURL (native PHP) |
| Data Source | Binance REST API (`api.binance.com/api/v3/klines`) |
| Testing | PHPUnit 10.5+ / 11.2+ |
| Code Style | `codeigniter/coding-standard` + PHP-CS-Fixer |
| Package Manager | Composer |
| Deployment | GitHub Actions → FTP + manual SCP |

### Required PHP Extensions
- `ext-intl`
- `ext-mbstring`
- `ext-mysqlnd` (for MySQLi driver)
- `ext-curl` (for Binance API calls)
- `ext-json`

### Dev Dependencies
- `phpunit/phpunit`
- `fakerphp/faker`
- `friendsofphp/php-cs-fixer`
- `kint-php/kint` (debugging)
- `mikey179/vfsstream` (virtual filesystem tests)

---

## Directory Structure

```
app/
  Commands/         # Custom spark CLI commands (e.g., ImportBinance)
  Config/           # CI4 configuration (Routes, Database, Filters, etc.)
  Controllers/      # HTTP controllers — C_ prefix
  Database/         # Migrations / Seeds (mostly empty; schema managed via .sql files)
  Filters/          # HTTP filters (auth, admin, RememberMe, PublicPath)
  Helpers/          # Helper functions
  Language/         # Localization strings
  Libraries/        # Custom libraries
  Models/           # DB models — M_ prefix
  ThirdParty/       # Third-party integrations
  Views/            # Templates — V_ prefix
      admin/        # Admin-only views
      errors/       # Error page templates

docs/               # Project documentation (see Documentation Structure below)
public/             # Web root — index.php, assets (css/, images/)
system/             # CodeIgniter 4 framework core
tests/              # PHPUnit tests (unit/, database/, session/)
vendor/             # Composer dependencies
writable/           # CI4 writable dir (cache, logs, session, uploads)
```

---

## Build and Test Commands

### Tests
```bash
# Run full test suite
vendor/bin/phpunit

# Run a single test file
vendor/bin/phpunit tests/path/to/TestFile.php

# Composer script alias
composer test
```

### Local Development Server
```bash
php spark serve
```

### CodeIgniter CLI (spark)
```bash
php spark routes              # List all routes
php spark migrate             # Run migrations
php spark db:seed <Seeder>    # Run seeders
```

### Custom CLI — Binance Import
```bash
php spark db:import                          # all coins × all timeframes (100 days)
php spark db:import --coin BTCUSDT           # single coin
php spark db:import --timeframe 1h           # single timeframe
php spark db:import --days 30               # custom lookback
php spark db:import --daily                  # today only (overrides --days)
```

### Code Style
```bash
# PHP-CS-Fixer is installed; run manually via:
vendor/bin/php-cs-fixer fix --config=.php-cs-fixer.dist.php
```
There is no pre-commit hook configured; style fixes are manual.

---

## Code Style Guidelines

### Naming Conventions
| Construct | Pattern | Example |
|-----------|---------|---------|
| Controllers | `C_` prefix | `C_Database`, `C_View`, `C_Auth`, `C_PSAR` |
| Models | `M_` prefix | `M_Coin_Data`, `M_Auth`, `M_Users` |
| Views | `V_` prefix | `V_Home.php`, `V_Dashboard.php`, `V_Login.php` |
| Methods | camelCase | `Binance_Import()`, `get_list_coin()` |
| Array keys | snake_case | `close_price`, `open_time` |
| Constants | UPPER_SNAKE_CASE | `TIMEFRAMES` |
| Class properties | snake_case or camelCase (be consistent) | `$M_Coin_Data` |

### Constructor Pattern
Instantiate models in `__construct` (not via dependency injection):
```php
public function __construct()
{
    $this->M_Coin_Data = new M_Coin_Data();
}
```

### Type Casting
Always cast URI segments and user input before use:
```php
$days = (int)$days;
$coin_id = (int)$coin_id;
```

### Query Direction Convention
- `ASC` (oldest first) — for calculations, MA/PSAR sliding windows
- `DESC` (newest first) — for UI display and candlestick charts

### Time Handling
Binance uses **milliseconds** throughout:
```php
$startTime = strtotime('-100 days') * 1000;   // PHP → ms
$date = date('Y-m-d', $kline[0] / 1000);      // ms → PHP
$msPerDay = 86400000;
```

### Duplicate Prevention
Use `INSERT IGNORE` via composite unique key `(id_coin, timeframe, open_time)`:
```php
$model->insertBatchIgnore($batch);   // bulk preferred
```
Or check before single inserts:
```php
$exists = $M_Coin_Data->where('id_coin', $id)->where('timeframe', $tf)->where('open_time', $t)->countAllResults();
if ($exists == 0) { $M_Coin_Data->insert([...]); }
```

### cURL Error Handling
Always validate before `json_decode`:
```php
if ($response === false) { /* curl_error($ch) */ }
$data = json_decode($response, true);
if (isset($data['code'])) { /* Binance API error */ }
```

### Docblocks
Every public method should have a docblock explaining purpose, parameters, and return values. See `C_Database.php` and `C_PSAR.php` for examples.

---

## Testing Instructions

### Test Configuration
- **Config file**: `phpunit.xml.dist`
- **Bootstrap**: `system/Test/bootstrap.php`
- **Test database**: SQLite3 in-memory (`:memory:`) with prefix `db_`
- **Coverage**: Includes `app/` directory; excludes `app/Views/` and `app/Config/Routes.php`
- **Cache dir**: `build/.phpunit.cache`

### Test Suites
```xml
<testsuite name="App">
    <directory>./tests</directory>
</testsuite>
```

### Writing Tests
- Place database tests in `tests/database/`
- Place unit tests in `tests/unit/`
- Extend `CodeIgniter\Test\CIUnitTestCase` or the framework's test base classes
- Tests use the SQLite3 `tests` connection group automatically when `ENVIRONMENT === 'testing'` (see `app/Config/Database.php`)

### Current Test Coverage
The project has minimal test coverage at the moment. Existing examples:
- `tests/unit/HealthTest.php`
- `tests/database/ExampleDatabaseTest.php`
- `tests/database/C_Database_MA20_MA50_Close_Test.php` (empty file)

---

## Architecture

### MVC Layer Map
| Layer | Prefix | Location | Role |
|-------|--------|----------|------|
| Controllers | `C_` | `app/Controllers/` | HTTP handling, API orchestration |
| Models | `M_` | `app/Models/` | DB queries only |
| Views | `V_` | `app/Views/` | Templates |
| Filters | — | `app/Filters/` | Auth/admin guards |
| Commands | — | `app/Commands/` | CLI spark commands |

### Key Controllers
- **`C_Database`** — Binance import pipeline, MA20/MA50 calculation
- **`C_PSAR`** — Parabolic SAR batch calculation (5 parameter sets A–E across all coins × timeframes)
- **`C_View`** — Renders all pages (delegates to views)
- **`C_Auth`** — Login, register, logout, forgot-password, throttling

### Key Models
- **`M_Coin_Data`** — All queries against `btcdatadb` and `tbl_coin`
- **`M_Auth`** — Queries against `auth` table
- **`M_Users`** — User profile and role management (`users` table)

### Filters
- **`auth`** — Requires valid session (applied to `/dashboard`, `/database/*`)
- **`admin`** — Requires `role = 'Admin'` (applied to all `/import*` routes)
- **`rememberme`** — Auto-login from remember-me cookie
- **`publicpath`** — Redirects `/public/*` requests

CSRF protection is applied only to `auth/*` POST endpoints (see `app/Config/Filters.php`).

### Routes (`app/Config/Routes.php`)
```
GET       /public/(:any)                       → C_View::Public_Redirect
GET/POST  /                                    → C_View::Home
GET/POST  /dashboard                           → C_View::Dashboard              [filter: auth]
GET/POST  /database/(:segment)/(:segment)/(:num) → C_View::Database            [filter: auth]
GET       /login                               → C_View::Login
GET       /login/forgot-password               → C_View::Forgot_Password
POST      /auth/login                          → C_Auth::Login_Post
POST      /auth/forgot-password                → C_Auth::Forgot_Password
GET       /logout                              → C_Auth::Logout
GET       /register                            → C_View::Register
POST      /auth/register                       → C_Auth::Register_Post
GET/POST  /importbinance                       → C_Database::Binance_Import      [filter: admin]
POST      /importbinancedaily                  → C_Database::Binance_Daily_Import [filter: admin]
POST      /importma20                          → C_Database::MA20                [filter: admin]
POST      /importma50                          → C_Database::MA50                [filter: admin]
GET/POST  /importpsar                          → C_PSAR::PSAR_Batch              [filter: admin]
```

### Data Pipeline (Sector 1 → 2 → 3 → 4)
1. **Import** (`Binance_Import` / `Binance_Daily_Import` / `db:import` CLI): cURL → `api.binance.com/api/v3/klines` → INSERT IGNORE into `btcdatadb`. Uses composite unique key `(id_coin, timeframe, open_time)` for deduplication. Paginates up to 1000 candles per request.
2. **MA Indicators** (`MA20` / `MA50`): Sliding window over `close_price` ASC, batch `updateBatch()` for efficiency.
3. **PSAR** (`C_PSAR::PSAR_Batch`): Wilder Parabolic SAR calculated for 5 parameter sets (A–E) across every coin × timeframe combination. Stores `value`, `trend` (1=bull/0=bear), `af`, and `ep` per set for incremental resumability.
4. **Display** (`C_View::Database`): Reads `btcdatadb` ASC for chart data, passes JSON-encoded arrays to views.

### PSAR Parameter Sets
| Set | AF Start | AF Step | AF Max | Profile |
|-----|----------|---------|--------|---------|
| A | 0.01 | 0.01 | 0.10 | Conservative |
| B | 0.02 | 0.02 | 0.20 | Standard (Wilder) |
| C | 0.02 | 0.02 | 0.30 | Moderate |
| D | 0.03 | 0.03 | 0.30 | Aggressive |
| E | 0.05 | 0.05 | 0.40 | Fast |

---

## Database Schema

> **Authoritative SQL files** live in `docs/00_ARCHITECTURE_FOUNDATION/00_01_DATABASE/`. Always verify field names against those files before writing queries.

### `btcdatadb` — OHLCV kline data + indicators
```
id, id_coin, timeframe (VARCHAR 4), date (VARCHAR YYYY-MM-DD),
open_time (BIGINT ms), open_price, high_price, low_price, close_price, volume,
close_time (BIGINT ms), quote_volume, number_of_trades, taker_base_volume,
taker_quote_volume, ma20, ma50, crossed_ma20_ma50,
psar_a_value, psar_a_trend, psar_a_af, psar_a_ep,
psar_b_value, psar_b_trend, psar_b_af, psar_b_ep,
psar_c_value, psar_c_trend, psar_c_af, psar_c_ep,
psar_d_value, psar_d_trend, psar_d_af, psar_d_ep,
psar_e_value, psar_e_trend, psar_e_af, psar_e_ep
```
- **Unique key**: `(id_coin, timeframe, open_time)`
- **Index**: `(id_coin, timeframe, date)`

### `tbl_coin` — Supported coins
```
id_coin, coinname (BTCUSDT/ETHUSDT/SOLUSDT/BNBUSDT), ghichu
```

### `auth` — Authentication
```
id, username, email, password, last_login, created_at, verified, verification_token,
verification_expires_at, remember_selector, remember_hash, remember_expires_at
```

### `users` — Profiles and roles
```
id, description, first_name, last_name, display_name, status, role (Admin|Moderator|User|Guest), created_at
```

### Critical Field Names
Do not guess field names. Common mistakes:

| Wrong | Correct |
|-------|---------|
| `coin_id` | `id_coin` |
| `price` | `open_price` or `close_price` |
| `ts` | `open_time` or `close_time` |
| `avg20` | `ma20` |
| `interval` | `timeframe` |

---

## Frontend Conventions

### Design System Tokens
All pages share a consistent dark-theme token set:
```css
:root {
    --bg:      #0B1426;
    --surface: rgba(255,255,255,0.04);
    --border:  rgba(255,255,255,0.09);
    --accent:  #38BDF8;
    --green:   #34D399;
    --red:     #F87171;
    --muted:   rgba(255,255,255,0.45);
    --radius:  12px;
}
```

### Fonts
- `Plus Jakarta Sans` — body text everywhere
- `Space Grotesk` — hero/display headings (`V_Home.php`)
- `JetBrains Mono` — ticker strip, monospace data

Load via `<link>` tags (never `@import` inside `<style>`, it's render-blocking).

### View → CSS Map
| View | Stylesheet(s) |
|------|--------------|
| `V_Home.php` | inline `<style>` block (self-contained) |
| `V_Login.php` | `public/assets/css/auth.css` |
| `V_Register.php` | `public/assets/css/auth.css` |
| `V_Dashboard.php` | `public/assets/css/dashboard.css` |
| `V_Footer.php` | `public/assets/css/footer.css` (self-loading via `<link>`) |

### Contour Canvas Animation
Marching-squares wave background used on `V_Home.php`, `V_Login.php`, `V_Register.php`:
- `CELL = 18` — grid resolution
- `LEVELS = 6` — number of contour bands (7 on home page)
- `t += 0.003` — animation speed
- Start `t = Math.random() * 100` for unique start each load
- Throttle auth pages to ~45fps; home page runs at 60fps
- Always guard with `if (document.hidden) return` to pause when tab is hidden

### Performance Notes
- Google Fonts: always use `<link rel="preconnect">` + `<link rel="stylesheet">`, never `@import`
- Orb blurs capped at `blur(80px)` + `will-change: transform`
- Canvas pauses on `document.hidden`

---

## Security Considerations

### Authentication & Authorization
- Passwords are hashed (bcrypt) before storage in the `auth` table.
- Session-based auth with optional "remember me" cookie.
- `AdminFilter` checks the `users.role` column in the database on every request — roles are **not** stored in the session to prevent spoofing.
- `AuthFilter` guards user-facing routes (`/dashboard`, `/database/*`).
- `AdminFilter` guards data-mutation routes (`/importbinance`, `/importma20`, `/importma50`, `/importpsar`).

### CSRF
- CSRF filter is applied **only** to `auth/*` POST endpoints (see `app/Config/Filters.php`).
- It is **not** enabled globally; admin import endpoints rely on session + role checks instead.

### Input Validation
- URI segments are always cast with `(int)` or validated against allow-lists (e.g., `$validTimeframes`) before use.
- SQL injection is mitigated by CodeIgniter's query builder and parameterized queries. The only raw SQL is `INSERT IGNORE` in `M_Coin_Data::insertBatchIgnore()`, which uses bound parameters.

### Deployment Security
- The `public/` folder is the web root; `app/`, `system/`, `writable/` should never be web-accessible.
- `DBDebug` is set to `true` in `app/Config/Database.php` — this should be `false` in production to avoid leaking SQL errors.
- `.env` is present and gitignored; credentials should live there, not in committed config files.

### Secrets Management
- Database credentials in `app/Config/Database.php` are hard-coded. The project also has a `.env` file that is gitignored. Agents should prefer `.env` values and remind maintainers to rotate hard-coded credentials.
- FTP deployment credentials are stored in GitHub Secrets (`FTP_SERVER`, `FTP_USERNAME`, `FTP_PASSWORD`).

---

## Deployment

### GitHub Actions (Primary)
File: `.github/workflows/deploy.yml`
- Triggers on push to `main` or manual dispatch
- Uses `SamKirkland/FTP-Deploy-Action@v4.3.5`
- Excludes: `.git*`, `tests/`, `vendor/`, `system/`, `writable/`, `docs/`, `.github/`, `.claude/`, `composer.phar`, `composer.lock`

### Manual SCP (Secondary)
Server: `giaphuc_dev@103.77.160.104` port `2229`
Root: `/home/thuytrieu.vn/giaphuc/`

```bash
scp -P 2229 app/Views/V_Home.php giaphuc_dev@103.77.160.104:/home/thuytrieu.vn/giaphuc/app/Views/
scp -P 2229 public/assets/css/auth.css giaphuc_dev@103.77.160.104:/home/thuytrieu.vn/giaphuc/public/assets/css/
```

**Writable subdirs** (deploy freely): `app/Views/`, `app/Controllers/`, `app/Models/`, `app/Filters/`, `app/Config/`, `app/Commands/`, `public/`

**Restricted** (different server user): `app/Helpers/`, `app/Language/`, `app/Libraries/`, `app/ThirdParty/`

---

## Documentation Structure

```
docs/
├── 00_ARCHITECTURE_FOUNDATION/    # Tier A: system laws, database schema (.sql files)
│   └── 00_01_DATABASE/            # Authoritative .sql files + stored procedures
├── 01_ENGINE_DOMAIN/              # Tier B: business logic per engine
│   ├── 01_BINANCE_API_ENGINE/
│   └── 03_INDICATOR_ENGINE/
├── 02_API_CONTRACTS/              # API specs (import endpoints, display endpoints)
├── 07_OPS_DEVOPS/                 # Deployment guide, UI sync, git workflow
└── 09_ROADMAP/                    # Project roadmap, PSAR research, multi-timeframe architecture
```

Naming pattern for docs: `<tier>_<index>_<slug>.md`

---

## Key Files for Agents

| File | Purpose |
|------|---------|
| `app/Config/Routes.php` | All HTTP routes |
| `app/Config/Database.php` | DB connections (default = MySQL, tests = SQLite3) |
| `app/Config/Filters.php` | Filter aliases and global filter rules |
| `app/Controllers/C_Database.php` | Binance import, MA20/MA50, convergence detection |
| `app/Controllers/C_PSAR.php` | Parabolic SAR batch calculation |
| `app/Controllers/C_View.php` | Page rendering, chart data preparation |
| `app/Controllers/C_Auth.php` | Authentication logic |
| `app/Models/M_Coin_Data.php` | All crypto data queries |
| `app/Models/M_Auth.php` | Auth table queries |
| `app/Models/M_Users.php` | User/role queries + achievements |
| `app/Commands/ImportBinance.php` | `db:import` CLI command |
| `phpunit.xml.dist` | PHPUnit configuration |
| `composer.json` | Dependencies and autoloading (`CodeIgniter\` → `system/`) |
| `.github/workflows/deploy.yml` | CI/CD FTP deployment |

---

## Development Team Context

- **Project Lead**: Phuc (admin, architecture decisions)
- **Developers**: 4 team members (learning phase)
- **Skill Level**: Junior — instructions should be clear, detailed, and include explanations.

When suggesting changes, prefer:
1. Explicit, step-by-step explanations
2. Batch operations over N+1 queries
3. Docblocks on every public method
4. Inline comments for non-obvious logic
5. Type casting before math operations
