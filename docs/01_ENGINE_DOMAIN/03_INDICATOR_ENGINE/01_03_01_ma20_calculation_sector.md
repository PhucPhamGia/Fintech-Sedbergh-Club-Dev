# Sector 2: Technical Indicators — Moving Averages

**Engine**: INDICATOR_ENGINE  
**Tier**: B (Business Logic)  
**Status**: ✅ MA20 Complete, 🔄 MA50 WIP

---

## **Purpose**

Calculate 20-period (MA20) and 50-period (MA50) moving averages for all cryptocurrencies based on historical closing prices.

---

## **URLs**

- MA20: `http://giaphuc.thuytrieu.vn/public/importma20`
- MA50: `http://giaphuc.thuytrieu.vn/public/importma50` (WIP)

---

## **MA20 Flow Diagram**

| **Entry Point** |
|:---|
| User accesses `/public/importma20` |
| → Routes to `C_Database::MA20()` |

↓

| **Load Data** |
|:---|
| Load coins from `tbl_coin` |
| For each coin: fetch all kline records (ordered by `open_time` ASC) |
| Skip if < 41 records (insufficient data) |

↓

| **Calculate Sliding Window** |
|:---|
| Initialize: empty window, sum = 0.0, updates batch = [] |
| For each candle: |
| - When window = 40 elements: MA20 = sum / 40 (round 8 decimals) |
| - If different from stored: add to batch |
| - Add close_price to window + sum |
| - If window > 40: remove oldest (shift), subtract from sum |

↓

| **Batch Update** |
|:---|
| Execute single `updateBatch()` (all updates at once) |
| Redirect to `/database/1/50` |

---

## **MA20 Technical Specs**

- **Window size**: 40 candles
- **Represents**: 20 periods (12h intervals = 10 days)
- **Formula**: `MA20 = SUM(last 40 close_prices) / 40`
- **Precision**: 8 decimals
- **Optimization**: Only update if value changed
- **Algorithm**: Sliding window (O(n), not O(n²))

---

## **MA50 Technical Specs**

- **Window size**: 100 candles
- **Represents**: 50 periods (12h intervals = 25 days)
- **Formula**: `MA50 = SUM(last 100 close_prices) / 100`
- **Precision**: 8 decimals
- **Status**: Functional but may need optimization
- **Algorithm**: Same sliding window technique

---

**Related**:
- `01_BINANCE_API_ENGINE/` - Data import
- `04_STORAGE_ENGINE/` - Batch update patterns
- `00_01_DATABASE/00_01_01_btcdatadb_kline_data.sql` - `ma20`, `ma50` fields