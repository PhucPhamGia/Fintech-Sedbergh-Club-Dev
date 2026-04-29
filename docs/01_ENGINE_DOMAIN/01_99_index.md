# ENGINE DOMAIN — Index

**Tier**: B (Business Logic)  
**Status**: ✅ Complete & Operational

---

## **Engines by Pipeline Order**

| P2 | Engine | Status | Files |
|----|--------|--------|-------|
| 01 | BINANCE_API_ENGINE | ✅ Complete | 3 docs |
| 02 | DATA_PROCESSING_ENGINE | ✅ Complete | 3 docs |
| 03 | INDICATOR_ENGINE | ✅ MA20, 🔄 MA50 | 4 docs |
| 04 | STORAGE_ENGINE | ✅ Complete | 3 docs |
| 05 | USER_ENTITLEMENT | 🔄 WIP | (auth module) |

---

## **Engine Details**

### **01. BINANCE_API_ENGINE** - Data Collection Layer
Fetches kline data from Binance API (12h intervals)

### **02. DATA_PROCESSING_ENGINE** - Validation & Transform Layer
Validates, deduplicates, extracts date from klines

### **03. INDICATOR_ENGINE** - Technical Analysis Layer
Calculates MA20 (complete), MA50 (WIP)

### **04. STORAGE_ENGINE** - Database Operations Layer
Insert, update, query operations on `btcdatadb`

### **05. USER_ENTITLEMENT** - Access Control Layer
Authentication, user roles, quotas (planned for future)

---

**Related**:
- `01_00_engine_pipeline_canonical_order.md` - Detailed pipeline
- `docs/02_API_CONTRACTS/` - API specifications
- `docs/00_ARCHITECTURE_FOUNDATION/` - Laws