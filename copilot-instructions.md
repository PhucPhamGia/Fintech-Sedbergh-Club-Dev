# Copilot Instructions for CI4 Crypto Data Project

## Project Overview
CodeIgniter 4 project for importing and analyzing cryptocurrency kline data from Binance API with technical indicators (MA20, MA50).

---

## 📂 Documentation Organization & Naming Rules

### **Architecture Foundation (Tier A)**
All system laws, principles, and foundational architecture documents.

**Location**: `docs/00_ARCHITECTURE_FOUNDATION/`

**File Naming**: `00_<INDEX>_<DOC_SLUG>.md`

Examples:
- `00_00_docs_storage_architecture_lawbook.md` - This lawbook
- `00_01_database_schema_canonical.md` - Database structure
- `00_02_api_integration_principles.md` - API standards
- `00_03_code_conventions_ci4.md` - Coding conventions
- `00_99_index.md` - Index & navigation

---

### **Engine & Domain Layer (Tier B)**
Business logic, data processing engines, and domain-specific rules.

**Location**: `docs/01_ENGINE_DOMAIN/`

**Folder Structure**:
```
docs/01_ENGINE_DOMAIN/
├── 01_BINANCE_API_ENGINE/        [Layer 1: API Data Collection]
├── 02_DATA_PROCESSING_ENGINE/    [Layer 2: Data Transformation]
├── 03_INDICATOR_ENGINE/          [Layer 3: Technical Indicators (MA20/MA50)]
├── 04_STORAGE_ENGINE/            [Layer 4: Database Operations]
└── 05_USER_ENTITLEMENT/          [Layer 5: User Access & Quotas]
```

**File Naming within each folder**: `01_<LAYER>_<INDEX>_<DOC_SLUG>.md`

Examples:
- `docs/01_ENGINE_DOMAIN/01_BINANCE_API_ENGINE/01_01_01_binance_klines_api.md`
- `docs/01_ENGINE_DOMAIN/02_DATA_PROCESSING_ENGINE/01_02_01_duplicate_prevention.md`
- `docs/01_ENGINE_DOMAIN/03_INDICATOR_ENGINE/01_03_01_ma20_calculation_rules.md`

---

### **Delivery & Surface Layer (Tier C)**
APIs, UI/UX, Admin tools, and deployment documentation.

**Locations**:
- `docs/02_API_CONTRACTS/` - API specifications
- `docs/04_UI_UX/` - Frontend documentation
- `docs/05_ADMIN/` - Admin tools & operations
- `docs/07_OPS_DEVOPS/` - Deployment & DevOps

---

## Architecture & Code Organization

### **Controller Structure** (app/Controllers/)
- Prefix: `C_` (e.g., `C_Database`)
- Methods: camelCase
- Purpose: HTTP request handling & API orchestration

### **Model Structure** (app/Models/)
- Prefix: `M_` (e.g., `M_Coin_Data`)
- Methods: camelCase + snake_case arrays
- Purpose: Database queries & business logic
- Allowed Fields (btcdatadb): 
  ```
  id, date, id_coin, open_time, open_price, high_price, low_price,
  close_price, volume, close_time, quote_volume, number_of_trades,
  taker_base_volume, taker_quote_volume, ma20, ma50
  ```

---

## Database Schema (btcdatadb)

| Field | Type | Purpose |
|-------|------|---------|
| id | INT | Primary key |
| id_coin | INT | Foreign key to tbl_coin |
| date | VARCHAR | Date (YYYY-MM-DD) |
| open_time | BIGINT | Binance timestamp (milliseconds) |
| open_price | DECIMAL | Opening price |
| high_price | DECIMAL | Highest price in period |
| low_price | DECIMAL | Lowest price in period |
| close_price | DECIMAL | Closing price |
| volume | DECIMAL | Trading volume |
| close_time | BIGINT | Binance close timestamp |
| quote_volume | DECIMAL | Quote asset volume |
| number_of_trades | INT | Number of trades |
| taker_base_volume | DECIMAL | Taker base volume |
| taker_quote_volume | DECIMAL | Taker quote volume |
| ma20 | DECIMAL | 20-period moving average |
| ma50 | DECIMAL | 50-period moving average |

---

## Model Methods (M_Coin_Data)

### Query Methods
| Method | Parameters | Returns | Notes |
|--------|------------|---------|-------|
| `get_list_coin()` | — | Array | All coins from tbl_coin |
| `get_coinname_by_id($id_coin)` | int | String | Single coin name |
| `get_all_data()` | — | Array | All kline records |
| `get_data_by_coin_id($coin_id, $limit)` | int, int | Array | Records by coin, ASC time |
| `get_data_for_candlestickchart($id_coin, $limit)` | int, int | Array | OHLC data, DESC time |
| `get_data_by_coin_id_n_day($coin_id, $days)` | int, int | Array | Last N days, ASC time |
| `get_ma20($limit, $coin_id)` | int, int | Array | MA indicators, DESC time |

### Insert/Update Methods
- `insert(array $data)` - Add new kline record
- `update(int $id, array $data)` - Update existing record

---

## Controller Methods (C_Database)

| Method | Purpose | Interval | Scope |
|--------|---------|----------|-------|
| `Binance_Import()` | Import historical data | 12h | Last 100 days |
| `Binance_Daily_Import()` | Daily import | 12h | Today only |
| `MA20()` | Calculate 20-period MA | — | Batch operation |
| `MA50()` | Calculate 50-period MA | — | WIP |

---

## Key Implementation Patterns

### 1. **Duplicate Prevention**
```php
$exists = $M_Coin_Data->where('date', $date)
    ->where('id_coin', $coin_item['id_coin'])
    ->where('open_time', $kline[0])
    ->countAllResults();
if ($exists == 0) {
    $M_Coin_Data->insert([...]);
}
```

### 2. **API Integration (Binance Klines)**
- **Endpoint**: `https://api.binance.com/api/v3/klines`
- **Method**: GET via cURL
- **Parameters**: symbol, interval, startTime, endTime, limit=1000
- **Response**: Array of 13-element kline arrays
- **Error Handling**: Check `if (isset($data['code']))`

### 3. **Time Handling**
- Binance uses milliseconds: `strtotime() * 1000`
- Database stores as milliseconds
- Date extraction: `date('Y-m-d', $kline[0] / 1000)`
- Day calculation: `86400000` ms per day

### 4. **Query Direction**
- **ASC (oldest first)**: For calculations, historical analysis
- **DESC (newest first)**: For candlestick display, real-time views

---

## Code Conventions

### Naming
- **Controllers**: `C_` prefix
- **Models**: `M_` prefix
- **Methods**: camelCase
- **Array keys**: snake_case
- **Constants**: UPPER_SNAKE_CASE

### Type Casting
- Always cast limit/days to int: `$limit = (int)$limit;`
- Validate before database queries

### Constructor Pattern
```php
protected $M_Coin_Data;
public function __construct() {
    $this->M_Coin_Data = new M_Coin_Data();
}
```

---

## When Assisting

✅ **DO**:
- Refactor repeated cURL logic into helper methods
- Use batch operations for moving average updates
- Suggest database index optimization (id_coin + open_time)
- Verify parameter casting to int
- Check timezone consistency (Binance = UTC)
- Ensure DESC queries for UI, ASC for calculations

❌ **DON'T**:
- Violate duplicate prevention rules
- Mix query directions without comment
- Insert unvalidated API data
- Ignore type casting for database queries
- Skip error handling for cURL responses

---

## File Locations Reference

| Component | Location |
|-----------|----------|
| Controllers | `app/Controllers/` |
| Models | `app/Models/` |
| Database Migrations | `app/Database/Migrations/` |
| Configuration | `app/Config/` |
| Documentation | `docs/` |
| Views (if any) | `app/Views/` |

---

## Related Documentation

- **Database Schema**: `docs/00_ARCHITECTURE_FOUNDATION/00_01_database_schema_canonical.md`
- **API Standards**: `docs/00_ARCHITECTURE_FOUNDATION/00_02_api_integration_principles.md`
- **Binance API Engine**: `docs/01_ENGINE_DOMAIN/01_BINANCE_API_ENGINE/01_01_01_binance_klines_api.md`
- **Code Conventions**: `docs/00_ARCHITECTURE_FOUNDATION/00_03_code_conventions_ci4.md`

---

## Last Updated
January 18, 2026

- **Controllers**: `app/Controllers/C_Database.php` - API imports and data processing
- **Models**: `app/Models/M_Coin_Data.php` - Database queries and calculations
- **Tables**: `btcdatadb` (kline data), `tbl_coin` (coin list)

## Database Schema (btcdatadb)
```
id, id_coin, date, open_time, open_price, high_price, low_price, close_price,
volume, close_time, quote_volume, number_of_trades, taker_base_volume,
taker_quote_volume, ma20, ma50
```

## Model Methods (M_Coin_Data)
Located in `app/Models/M_Coin_Data.php`:

- `get_list_coin()` - Returns all coins from tbl_coin as array
- `get_coinname_by_id($id_coin)` - Returns single coin name string
- `get_all_data()` - Returns all kline records from btcdatadb
- `get_data_by_coin_id($coin_id, $number_of_records)` - Returns records by coin, ascending time, limited
- `get_data_for_candlestickchart($id_coin, $limit)` - Returns OHLC data (date, open_price, high_price, low_price, close_price), descending time order
- `get_data_by_coin_id_n_day($coin_id, $days)` - Returns last N days of data, ascending time
- `get_ma20($limit, $coin_id)` - Returns date, ma20, ma50 indicators, descending time

## Model Configuration
- Table: `btcdatadb` (default)
- Primary Key: `id`
- Allowed Fields: id, date, id_coin, open_time, open_price, high_price, low_price, close_price, volume, close_time, quote_volume, number_of_trades, taker_base_volume, taker_quote_volume, ma20, ma50
- Reference Tables: `tbl_coin` for coin metadata

## Controller Methods (C_Database)
- `Binance_Import()` - Last 100 days, 12h interval, redirects to /database/1/50
- `Binance_Daily_Import()` - Today only, 12h interval
- `MA20()` - Calculate 20-period moving average (40 candles window)
- `MA50()` - Calculate 50-period moving average (WIP)

## Code Conventions
- Controllers: `C_` prefix
- Models: `M_` prefix
- Methods: camelCase
- Array keys: snake_case
- Use constructor injection: `$this->M_Coin_Data`
- Always validate cURL responses before JSON decode
- Check for API error codes: `if (isset($data['code']))`

## Query Patterns
1. **Table Selection**: Use `$this->db->table($this->table_tbl_coin)` or `$this->where()` chaining
2. **Order By**: Specify 'ASC' (oldest first) or 'DESC' (newest first) based on use case
3. **Filtering**: Chain `where()` and `orderBy()` before `findAll()`
4. **Type Casting**: Cast `$limit` and `$days` to int before use

## Key Patterns
1. **Duplicate Prevention**: Check existence before insert
   ```php
   $exists = $M_Coin_Data->where('date', $date)
       ->where('id_coin', $coin_item['id_coin'])
       ->where('open_time', $kline[0])
       ->countAllResults();
   if ($exists == 0) { $M_Coin_Data->insert([...]); }
   ```

2. **API Integration**: Binance klines endpoint
   - URL: `https://api.binance.com/api/v3/klines`
   - Params: symbol, interval, startTime, endTime, limit=1000
   - Response: Array of [open_time, open_price, ..., taker_quote_volume]

3. **Time Handling**:
   - Binance uses milliseconds: `strtotime() * 1000`
   - Database stores as milliseconds
   - Date extraction: `date('Y-m-d', $kline[0] / 1000)`
   - Day calculation: `86400000` milliseconds per day

4. **Moving Averages**: Window-based calculation with batch updates
   - MA20: 20-period window (40 candles for updates on each new candle)
   - Store in `ma20` and `ma50` columns

## When Assisting
- Refactor repeated cURL logic into helper methods
- Use batch operations for moving average updates
- Suggest index optimization for `id_coin + open_time` queries
- Flag SQL injection risks (validate input strictly)
- Recommend transaction handling for bulk imports
- Check timezone consistency (Binance = UTC)
- Verify parameter casting to int before database queries
- Ensure DESC queries for candlestick display, ASC for calculations

---

## 🗄️ **Database Schema Reference — AUTHORITATIVE SOURCE**

### **RULE: Always Reference #01_DATABASE for Field Names & Structure**

⚠️ **CRITICAL**: When reading, editing, or writing functions that interact with database tables:

1. **DO NOT GUESS** field names, types, or table structures
2. **ALWAYS READ** the canonical SQL schema files in `docs/00_ARCHITECTURE_FOUNDATION/01_DATABASE/`
3. **VERIFY** all field names, data types, constraints before writing code
4. **CROSS-CHECK** your code against the actual schema

### **Reference Files (Authoritative Source of Truth)**

| Table | Schema File | Purpose |
|-------|-------------|---------|
| `btcdatadb` | `docs/00_ARCHITECTURE_FOUNDATION/01_DATABASE/btcdatadb.sql` | Kline data (OHLCV + MA20/MA50) |
| `tbl_coin` | `docs/00_ARCHITECTURE_FOUNDATION/01_DATABASE/tbl_coin.sql` | Cryptocurrency list (BTC, ETH, SOL, BNB) |
| `auth` | `docs/00_ARCHITECTURE_FOUNDATION/01_DATABASE/auth.sql` | User authentication & login |
| Stored Procedures | `docs/00_ARCHITECTURE_FOUNDATION/01_DATABASE/stored_procedure/ma20.sql` | MA20 calculation procedure |

### **When Writing/Editing Code:**

**PROCESS**:
1. Identify which table(s) the function interacts with
2. Open the corresponding `.sql` file from `#01_DATABASE`
3. Extract field names, types, and constraints
4. Write code using **exact** field names from schema
5. Validate data types match (DECIMAL vs INT vs BIGINT vs VARCHAR)
6. Comment with schema reference

**EXAMPLE - BEFORE (❌ WRONG - GUESSING)**
```php
// ❌ DON'T: Guessing field names
$result = $M_Coin_Data->where('coin_id', $id)
    ->select('price, volume')
    ->findAll();
```

**EXAMPLE - AFTER (✅ RIGHT - READING SCHEMA)**
```php
// ✅ DO: Read from btcdatadb.sql → actual field is 'id_coin' not 'coin_id'
// Reference: docs/00_ARCHITECTURE_FOUNDATION/01_DATABASE/btcdatadb.sql
$result = $M_Coin_Data->where('id_coin', $id)
    ->select('open_price, volume')  // ← verified field names from schema
    ->findAll();
```

### **Field Name Reference (btcdatadb)**

**EXACT FIELD NAMES** (from #btcdatadb.sql):
```
id, id_coin, date, open_time, open_price, high_price, low_price, close_price,
volume, close_time, quote_volume, number_of_trades, taker_base_volume, 
taker_quote_volume, ma20, ma50
```

❌ **WRONG NAMES** (do NOT use):
- `coin_id` ← WRONG, use `id_coin`
- `price` ← WRONG, use `open_price` or `close_price`
- `ts` ← WRONG, use `open_time` or `close_time`
- `avg20` ← WRONG, use `ma20`

### **Field Name Reference (tbl_coin)**

**EXACT FIELD NAMES** (from #tbl_coin.sql):
```
id_coin, coinname, ghichu
```

| Field | Type | Values |
|-------|------|--------|
| `id_coin` | INT(11) | 1, 3, 4, 5 (auto-increment) |
| `coinname` | VARCHAR(50) | 'BTCUSDT', 'ETHUSDT', 'SOLUSDT', 'BNBUSDT' |
| `ghichu` | TEXT | Notes (optional) |

### **Field Name Reference (auth)**

**EXACT FIELD NAMES** (from #auth.sql):
```
id, username, email, password, last_login, created_at, verified, 
verification_token, verification_expires_at, remember_selector, 
remember_hash, remember_expires_at
```

### **When You're Wrong:**

If code fails due to field name mismatch:
1. Open the `.sql` file in `#01_DATABASE`
2. Find the correct field name
3. Update code with exact field name
4. Add comment: `// Reference: docs/00_ARCHITECTURE_FOUNDATION/01_DATABASE/{table}.sql`
5. Explain the correction

### **Stored Procedures Reference**

**MA20 Stored Procedure** (file: `#stored_procedure/ma20.sql`):
- Name: `ma20(desired_coin VARCHAR(50))`
- Purpose: Calculate average of 50 most recent close_price values
- Parameters: coin ID
- Returns: `ma20` (DOUBLE)
- Status: ⚠️ Note: This procedure uses `coin_id` parameter (verify compatibility with `id_coin` field)

---

## 🔗 **Summary: Three Rules**

1. **READ BEFORE CODING** — Check `#01_DATABASE` files first
2. **USE EXACT NAMES** — Copy-paste field names from `.sql` files, don't guess
3. **COMMENT REFERENCES** — Link code to schema file location

**Violation** = Technical debt, bugs, refactoring work for Team

---

## Last Updated
January 18, 2026