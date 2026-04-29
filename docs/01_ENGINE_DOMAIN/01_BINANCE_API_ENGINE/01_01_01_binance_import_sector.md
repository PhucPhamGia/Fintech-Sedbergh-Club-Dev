# Sector 1: Data Import — Binance API Integration

**Engine**: BINANCE_API_ENGINE  
**Tier**: B (Business Logic)  
**Status**: ✅ Complete

---

## **Purpose**

Fetch cryptocurrency kline data from Binance API and store into database.

Supports:
- ✅ Historical import (last 100 days)
- ✅ Daily import (today only)

---

## **URLs**

- Historical: `http://giaphuc.thuytrieu.vn/public/importbinance`
- Daily: `http://giaphuc.thuytrieu.vn/public/importbinancedaily`

---

## **Flow Diagram**

| **Entry Point** |
|:---|
| User accesses `/public/importbinance` or `/public/importbinancedaily` |
| → Routes to `C_Database::Binance_Import()` or `C_Database::Binance_Daily_Import()` |

↓

| **Build API Call** |
|:---|
| Load coin list from `tbl_coin` (BTC, ETH, SOL, BNB) |
| For each coin: construct Binance API URL |
| `https://api.binance.com/api/v3/klines?symbol=BTCUSDT&interval=12h&startTime=X&endTime=Y` |
| Execute cURL request |

↓

| **Validate & Error Handle** |
|:---|
| Check cURL errors: `if ($response === false)` → HTTP 500 |
| Parse JSON & check Binance errors: `if (isset($klines['code']))` → error message |

↓

| **Insert with De-duplication** |
|:---|
| For each kline: extract date from `open_time` (ms → YYYY-MM-DD) |
| Check composite key `(id_coin, date, open_time)` for duplicates |
| Insert if new → 13 fields to `btcdatadb` |
| Redirect to `/database/1/50` |

---

## **Key Details**

### **Time Handling**
- Binance format: Milliseconds (1000 ms = 1 second)
- Storage: Milliseconds (for precision)
- Calculation: `date('Y-m-d', $kline[0] / 1000)`
- Range: 
  - Historical: `strtotime('-100 days') * 1000` to now
  - Daily: `strtotime('today midnight') * 1000` to now
- Timezone: UTC (no conversion needed)

### **Duplicate Prevention**
```php
$exists = $M_Coin_Data->where('date', $date)
    ->where('id_coin', $coin_item['id_coin'])
    ->where('open_time', $kline[0])
    ->countAllResults();
if ($exists == 0) {
    $M_Coin_Data->insert([...]);
}
```

### **Error Handling**
- ✅ cURL failures: Check `if ($response === false)`
- ✅ API errors: Check `if (isset($klines['code']))`
- ✅ Both return HTTP 500 + JSON error message

---

**Related**: 
- `02_DATA_PROCESSING_ENGINE/` - Validation & duplicate logic
- `04_STORAGE_ENGINE/` - Database operations
- `00_01_DATABASE/00_01_01_btcdatadb_kline_data.sql` - Table schema