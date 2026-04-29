# API Contracts: Data Display Endpoints

**Tier**: C (Delivery/UI)  
**Status**: ✅ Complete

---

## **Endpoint: Display Kline Data with Chart**

**URL**: `GET /database/{coin_id}/{days}`

**Example**: `/database/1/50` (Coin 1 BTC, last 50 days)

**Parameters**:
| Param | Type | Required | Example |
|-------|------|----------|---------|
| `coin_id` | INT | ✅ Yes | 1 (BTC), 3 (ETH) |
| `days` | INT | ✅ Yes | 50 |
| `search_day` | INT | ⭕ Optional (POST) | 100 |

**Request Body** (Optional POST):
```json
{
  "search_day": 100
}
```

**Response**: HTML page with:
- 📊 Candlestick chart (OHLC)
- 📈 MA20 overlay line
- 📋 Data table (all klines)
- 🔍 Search form

---

## **Chart Data Formats**

### **Candlestick Format**
```json
[
  ["2026-01-18", 45123.45, 45200.00, 45150.50, 45300.75],
  ...
]
```
Format: `[date, low, open, close, high]`

### **MA20 Series Format**
```json
[
  ["2026-01-18", 45123.50],
  ...
]
```
Format: `[date, ma20_value]`

---

## **Flow**

1. User visits `/database/1/50`
2. Controller loads:
   - Raw klines (last 50 days)
   - Chart data (OHLCV)
   - MA20 data
3. View renders HTML + chart
4. If user posts search form → redirect to new `/database/1/{search_day}`

---

**Related**: `01_ENGINE_DOMAIN/04_STORAGE_ENGINE/` - Query patterns