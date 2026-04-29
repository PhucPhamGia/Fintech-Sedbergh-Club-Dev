# API Contracts: Data Import Endpoints

**Tier**: C (Delivery/API)  
**Status**: ✅ Complete

---

## **Endpoint 1: Historical Binance Import**

**URL**: `GET /public/importbinance`

**Purpose**: Import last 100 days of kline data at 12h intervals

**Method**: GET (no request body)

**Response** (Redirect):
- ✅ Success: `302 → /database/1/50` with message "Data imported successfully"
- ❌ Failure: `500 JSON` with error message

**Example Response (Error)**:
```json
{
  "error": "cURL Error: Connection timeout"
}
```

---

## **Endpoint 2: Daily Binance Import**

**URL**: `GET /public/importbinancedaily`

**Purpose**: Import today's kline data only at 12h intervals

**Method**: GET (no request body)

**Response** (Redirect):
- ✅ Success: `302 → /database/1/50` with message "Data imported successfully"
- ❌ Failure: `500 JSON` with error message

---

## **Endpoint 3: Calculate MA20**

**URL**: `GET /public/importma20`

**Purpose**: Calculate 20-period moving average for all coins

**Method**: GET (no request body)

**Response** (Redirect):
- ✅ Success: `302 → /database/1/50` with message "Data imported successfully"

---

## **Endpoint 4: Calculate MA50**

**URL**: `GET /public/importma50`

**Purpose**: Calculate 50-period moving average for all coins

**Method**: GET (no request body)

**Response** (Redirect):
- ✅ Success: `302 → /database/1/50` with message "Data imported successfully"
- ⚠️ Status: WIP

---

## **Error Code Taxonomy**

| HTTP Code | Scenario | Example |
|-----------|----------|---------|
| 500 | cURL failure | Network timeout, DNS failure |
| 500 | Binance API error | Invalid symbol, rate limited |
| 500 | Database error | Connection failed |

---

**Related**: `01_ENGINE_DOMAIN/01_BINANCE_API_ENGINE/` - Implementation details