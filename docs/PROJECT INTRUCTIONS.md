# Global Context

[giaphuc.thuytrieu.vn](http://giaphuc.thuytrieu.vn) is a foundation website to reach a final program that could automatically trade cryptocurrency to generate money. The primary goal is to build skill for the developers and make this as a project for university application. Then for the long term it is for the project to generate the developers income.

The developers of the website are not professional and still in the process of learning. So every instruction should be clear and detailed with explanation.

The project is running CodeIgniter 4 framework and MySQL database. Taking data from Binance API.

The user's role is admin and project leader of this website, managing a team of 4 developers.

---

# **Project's Sectors**

## **Sector 1: Data Import — Binance API Integration**

**URLs**: 
- Historical Import: `http://giaphuc.thuytrieu.vn/public/importbinance`
- Daily Import: `http://giaphuc.thuytrieu.vn/public/importbinancedaily`

### A1. Purpose

#### A1.1 Problem Statement
- What this module exists to do: Fetch cryptocurrency kline data from Binance API and store into the database
- Supports both **historical import** (last 100 days) and **daily import** (today only)

### A2. Flow Diagram

| **Entry Point (Routing → Controller)** |
|:---|
| User accesses `/public/importbinance` or `/public/importbinancedaily` |
| CodeIgniter 4 routes request to `C_Database::Binance_Import()` or `C_Database::Binance_Daily_Import()` |

↓

| **Build and Execute API Call (Controller → Binance)** |
|:---|
| Load coin list from `tbl_coin` (list of cryptos to fetch: BTC, ETH, SOL, BNB, etc.) |
| For each coin, construct Binance API URL: `https://api.binance.com/api/v3/klines` |
| Parameters: `symbol` (e.g., BTCUSDT), `interval` (12h), `startTime`, `endTime`, `limit` (1000) |
| **Binance_Import()**: Fetch last 100 days at 12h intervals (2 candles per day) |
| **Binance_Daily_Import()**: Fetch today only at 12h intervals |
| Execute cURL request and retrieve JSON response |

↓

| **Validate Response & Error Handling (Controller)** |
|:---|
| Check for cURL errors: `if ($response === false)` → return 500 error |
| Parse JSON response: `json_decode($response, true)` |
| Check for Binance API errors: `if (isset($klines['code']))` → return error message |

↓

| **Insert into Database with De-duplication (Controller → Model)** |
|:---|
| For each kline in response: |
| - Extract date from `open_time` (milliseconds → YYYY-MM-DD format) |
| - Check for duplicates using composite key: `(id_coin, date, open_time)` |
| - If record exists: skip (duplicate prevention) |
| - If new: insert all 13 fields into `btcdatadb` |
| Redirect to `/database/1/50` with success message |

### A3. Data Model

**Table**: `thuy_giaphuc.btcdatadb`

| Field | Type | Meaning | Source |
|-------|------|---------|--------|
| `id` | INT | Primary Key | Auto-increment |
| `id_coin` | INT | Foreign key to `tbl_coin` | Database |
| `date` | VARCHAR(10) | Trading date (YYYY-MM-DD) | Calculated from `open_time` |
| `open_time` | BIGINT | Binance start timestamp (ms since Jan 1, 1970) | Binance API |
| `open_price` | DECIMAL(18,8) | Price at candle open | Binance API |
| `high_price` | DECIMAL(18,8) | Highest price in interval | Binance API |
| `low_price` | DECIMAL(18,8) | Lowest price in interval | Binance API |
| `close_price` | DECIMAL(18,8) | Price at candle close | Binance API |
| `volume` | DECIMAL(18,8) | Total base asset volume | Binance API |
| `close_time` | BIGINT | Binance end timestamp (ms) | Binance API |
| `quote_volume` | DECIMAL(18,8) | Total quote asset volume | Binance API |
| `number_of_trades` | INT | Total executed trades | Binance API |
| `taker_base_volume` | DECIMAL(18,8) | Base volume by taker orders | Binance API |
| `taker_quote_volume` | DECIMAL(18,8) | Quote volume by taker orders | Binance API |
| `ma20` | DECIMAL(18,8) | 20-period moving average | Calculated |
| `ma50` | DECIMAL(18,8) | 50-period moving average | Calculated |

### A4. Key Implementation Details

#### Time Handling
- **Binance format**: Milliseconds (1000 ms = 1 second)
- **Database storage**: Milliseconds (for precise timestamp matching)
- **Date calculation**: `date('Y-m-d', $kline[0] / 1000)` converts ms to seconds then to date
- **Time range**: 
  - **Historical**: `strtotime('-100 days') * 1000` to current time
  - **Daily**: `strtotime('today midnight') * 1000` to current time
- **Timezone**: UTC (Binance operates on UTC, no conversion needed)

#### Duplicate Prevention
```php
$exists = $M_Coin_Data->where('date', $date)
    ->where('id_coin', $coin_item['id_coin'])
    ->where('open_time', $kline[0])
    ->countAllResults();
if ($exists == 0) {
    // Insert only if not found
    $M_Coin_Data->insert([...]);
}
```

#### Error Handling
- **cURL failure**: Check `if ($response === false)` → get error via `curl_error($ch)`
- **API error**: Check `if (isset($klines['code']))` → Binance returns error object
- **Both cases**: Return HTTP 500 with JSON error message

---

## **Sector 2: Technical Indicators — Moving Averages**

**URLs**:
- MA20 Calculation: `http://giaphuc.thuytrieu.vn/public/importma20`
- MA50 Calculation: `http://giaphuc.thuytrieu.vn/public/importma50` (WIP)

### B1. Purpose (MA20)

#### B1.1 Problem Statement
- What this module exists to do: Calculate 20-period moving average (MA20) for each cryptocurrency based on historical closing prices
- Update `ma20` column in `btcdatadb` for all records with sufficient historical data

### B2. Flow Diagram

| **Entry Point (Routing → Controller)** |
|:---|
| User accesses `/public/importma20` |
| CodeIgniter 4 routes request to `C_Database::MA20()` |

↓

| **Load Historical Price Data (Model → Controller)** |
|:---|
| Load coin list from `tbl_coin` (list of cryptocurrencies to process) |
| For each coin: |
| - Retrieve all historical price records ordered by `open_time` ASC (oldest → newest) |
| - Skip coin if total records < 41 (insufficient data for MA20 calculation) |

↓

| **Calculate MA20 Using Sliding Window (Controller Algorithm)** |
|:---|
| Initialize: empty window array, sum accumulator = 0.0, updates batch array |
| For each candle record: |
| - When window has exactly 40 elements: |
|   • Calculate MA20 = sum / 40 (rounded to 8 decimals) |
|   • Compare with stored `ma20` value |
|   • If different: add to updates batch `{id: row.id, ma20: calculated_ma20}` |
| - Add current `close_price` to window and sum |
| - If window exceeds 40 elements: remove oldest (shift) and subtract from sum |

↓

| **Batch Update Database (Model)** |
|:---|
| If updates array is not empty: |
| - Execute single `updateBatch()` query (all updates at once) |
| - This is more efficient than N individual UPDATE queries |
| Redirect to `/database/1/50` with success message |

### B3. MA20 Technical Details

- **Window size**: 40 candles
- **Represents**: 20 periods (at 12h intervals = 10 days of historical data)
- **Formula**: `MA20 = SUM(close_price of last 40 candles) / 40`
- **Precision**: Rounded to 8 decimal places
- **Update condition**: Only update if new MA20 ≠ stored MA20 (optimization)
- **Algorithm**: Sliding window (O(n) time complexity, not O(n²))

### B4. MA50 Technical Details (WIP - Work In Progress)

- **Window size**: 100 candles
- **Represents**: 50 periods (at 12h intervals = 25 days of historical data)
- **Formula**: `MA50 = SUM(close_price of last 100 candles) / 100`
- **Precision**: Rounded to 8 decimal places
- **Status**: Functional but may need optimization for large datasets
- **Implementation**: Same sliding window technique as MA20

---

## **Sector 3: Data Display & Visualization**

**URL**: `https://giaphuc.thuytrieu.vn/public/database/{coin_id}/{days}`

**Example**: `https://giaphuc.thuytrieu.vn/public/database/1/50` (Coin 1, last 50 days)

### C1. Purpose

#### C1.1 Problem Statement
- What this module exists to do: 
  - Display cryptocurrency kline data from database for selected coin and time period
  - Show candlestick chart with MA20 overlay
  - Support search feature for users to input custom day range

### C2. Flow Diagram

| **Entry Point (Routing → Controller)** |
|:---|
| User accesses `/database/{coin_id}/{days}` |
| CodeIgniter 4 routes request to `C_Database::Database()` |

↓

| **Parse Input Parameters (URI + Form POST)** |
|:---|
| From URI segments: |
| - `coin_id` = segment(2) |
| - `days` = segment(3) |
| From form POST (optional): |
| - `search_day` = POST('search_day') |

↓

| **Handle Search Redirect (if search_day provided)** |
|:---|
| If user submitted search form with `search_day`: |
| - Cast to integer: `(int)$search_day` |
| - Redirect to: `/database/{coin_id}/{search_day}` |
| - Stop processing (page reloads with new day range) |

↓

| **Load Base Page Data (Controller → Model)** |
|:---|
| Fetch and store in `$data` for view: |
| - `coin_id`: selected coin |
| - `coinname`: coin symbol (e.g., "BTC") |
| - `days`: time period |
| - `record`: raw kline data from database |
| - `search_day`: last search value (for form pre-fill) |

↓

| **Prepare Candlestick Chart Dataset** |
|:---|
| Call `M_Coin_Data->get_data_for_candlestickchart($coin_id, $days)` |
| Returns records with: `date, open_price, high_price, low_price, close_price` |
| Transform to chart format: `[date, low, open, close, high]` |
| Reverse array order (newest → oldest for chart display) |
| JSON-encode with numeric-safe encoding |

↓

| **Prepare MA20 Indicator Dataset** |
|:---|
| Call `M_Coin_Data->get_ma20($days, $coin_id)` |
| Returns records with: `date, ma20` |
| Transform to series format: `[date, ma20_value]` |
| JSON-encode with numeric-safe encoding |

↓

| **Render View** |
|:---|
| Pass `$data` to view template |
| View renders: |
| - Data table (raw kline records) |
| - Candlestick chart with MA20 overlay |
| - Search form |

### C3. Chart Display Requirements

- **Candlestick format**: `[date, low, open, close, high]`
- **Order**: Newest candle first (DESC by `open_time`)
- **MA20 series**: `[date, ma20_value]`
- **Time period**: Last N days (user-selectable)

---

## **Sector 4: User Authentication (Log In / Sign Up)**

**URLs**:
- Sign Up: `https://giaphuc.thuytrieu.vn/public/signup`
- Log In: `https://giaphuc.thuytrieu.vn/public/login`
- Log Out: `https://giaphuc.thuytrieu.vn/public/logout`

**Status**: In development (target completion: January 5, 2026)

### D1. Purpose

#### D1.1 Problem Statement
- What this module exists to do:
  - Provide secure admin/user authentication (log in / log out)
  - Allow new user registration
  - Integrate with access control for downstream modules (dashboard, paid content, admin areas)

### D2. Features (Planned)

- [ ] User registration with email validation
- [ ] Secure password hashing (bcrypt)
- [ ] Log in with email + password
- [ ] Session management
- [ ] Log out functionality
- [ ] "Remember me" option (optional)
- [ ] Password reset flow (optional)
- [ ] Role-based access control (admin vs regular user)

### D3. Database Table (tbl_users - Planned)

| Field | Type | Purpose |
|-------|------|---------|
| `id` | INT | Primary Key |
| `email` | VARCHAR(255) | User email (unique) |
| `password` | VARCHAR(255) | Hashed password |
| `username` | VARCHAR(100) | Display name |
| `role` | VARCHAR(50) | admin / user |
| `created_at` | TIMESTAMP | Registration date |
| `updated_at` | TIMESTAMP | Last update |

### D4. Security Requirements

- ✅ Password hashing (never store plain text)
- ✅ CSRF token protection
- ✅ SQL injection prevention (use parameterized queries)
- ✅ Session timeout after inactivity
- ✅ Rate limiting on login attempts (prevent brute force)

---

## **Sector 5: Architecture & Documentation**

**Key Files**:
- `.copilot-instructions.md` - AI assistant guidelines and project conventions
- `docs/00_ARCHITECTURE_FOUNDATION/00_00_docs_storage_architecture_lawbook.md` - Documentation standards
- `docs/01_ENGINE_DOMAIN/` - Business logic and engine documentation

### E1. Code Structure

**Controllers** (`app/Controllers/`):
- `C_Database` - Data import, calculation, and display functions
- Future: `C_Auth` - Authentication logic
- Future: `C_Admin` - Admin dashboard

**Models** (`app/Models/`):
- `M_Coin_Data` - Queries for cryptocurrency data
- Future: `M_User` - User authentication queries
- Future: `M_Settings` - Configuration queries

**Views** (`app/Views/`):
- Data display templates
- Chart templates
- Auth templates (signup, login)
- Admin dashboard templates

### E2. Naming Conventions

- **Controllers**: Prefix `C_` (e.g., `C_Database`)
- **Models**: Prefix `M_` (e.g., `M_Coin_Data`)
- **Methods**: camelCase
- **Variables**: snake_case for arrays, camelCase for objects
- **Constants**: UPPER_SNAKE_CASE

### E3. Code Quality Standards

- ✅ Every function has a detailed docblock (purpose, flow, parameters, returns)
- ✅ Inline comments for complex logic
- ✅ Error handling for all external API calls
- ✅ Input validation before database queries
- ✅ Batch operations for performance (not individual queries)
- ✅ Type casting for numeric values before math operations

---

## **Development Team & Roles**

- **Project Lead**: Phuc (admin, architecture decisions)
- **Developers**: 4 team members (contributors)
- **Skill Level**: Learning phase (clear documentation & explanations required)

---

## **Current Project Status**

| Sector | Feature | Status | Priority |
|--------|---------|--------|----------|
| Data Import | Binance_Import() | ✅ Complete | HIGH |
| Data Import | Binance_Daily_Import() | ✅ Complete | HIGH |
| Indicators | MA20 Calculation | ✅ Complete | HIGH |
| Indicators | MA50 Calculation | 🔄 WIP | MEDIUM |
| Display | Data table view | ✅ Complete | HIGH |
| Display | Candlestick chart | ✅ Complete | HIGH |
| Authentication | Sign up / Log in | 🔄 In Dev | HIGH |
| Authentication | User roles & permissions | ⏳ Planned | MEDIUM |
| Admin | Dashboard | ⏳ Planned | MEDIUM |
| Trading | Auto-trade engine | ⏳ Planned | LOW |

---

## **Related Documentation Files**

- `.copilot-instructions.md` - AI instructions, naming rules, database schema
- `docs/00_ARCHITECTURE_FOUNDATION/00_00_docs_storage_architecture_lawbook.md` - Documentation architecture
- `docs/01_ENGINE_DOMAIN/01_BINANCE_API_ENGINE/` - Binance API integration docs
- `docs/01_ENGINE_DOMAIN/03_INDICATOR_ENGINE/` - Moving average calculation docs
- `app/Controllers/C_Database.php` - Source code with detailed function comments

---

**Last Updated**: January 18, 2026  
**Project URL**: http://giaphuc.thuytrieu.vn  
**Database**: thuy_giaphuc (MySQL)