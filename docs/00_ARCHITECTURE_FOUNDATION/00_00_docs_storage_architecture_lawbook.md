# Docs Storage Architecture & Naming Lawbook (v1)

---

## **1. DOCUMENT OBJECTIVES**

This document defines the **STORAGE ARCHITECTURE LAWS**, **DOCUMENTATION STANDARDS**, and **FILE/FOLDER NAMING RULES** for the entire CI4 Crypto Data Analysis project.

### **Main Objectives**

* Prevent documentation chaos as the project expands (API, Models, Engines, UI…)

* Help **GPT and Dev Team** always identify correctly:
  * Where documentation should be stored
  * How to name files
  * Clear distinction between **LAWS – ENGINES – DELIVERY**

* Enable expansion over **many years** without **breaking the structure**

⛔ Any violation of this document is considered **ARCHITECTURAL BREACH**.

---

## **1.5. LINK TO .COPILOT-INSTRUCTIONS.MD (IMPLEMENTATION)**

**Rules in this Lawbook are implemented through**:

📄 **File**: `/Users/PhamGiaPhuc/Desktop/ci4/.copilot-instructions.md`

**Related Sections**: 
- Section "📂 **Documentation Organization & Naming Rules**"
- Full reference to Lawbook
- Guidelines for AI coding agent

**Relationship**:
- **Lawbook** = Defines architecture laws (EFFECTIVE FOR ENTIRE PROJECT)
- **copilot-instructions** = Implementation guidelines for AI + Dev
- Both **MUST STAY IN SYNC**: If Lawbook changes → update copilot-instructions

---

## **2. FOUNDATION LAW — DOCUMENTATION MUST BE TIERED**

Each document **must** belong to **EXACTLY ONE TIER**.

### **TIER A — ARCHITECTURE / FOUNDATION (IMMUTABLE)**

Includes:
* Architecture laws (this lawbook)
* System principles
* Database schema canonical
* API standards
* Code conventions CI4
* Naming rules

✅ Used for:
* Training GPT
* Onboarding Developers
* Architecture review
* Preventing technical debt

---

### **TIER B — ENGINE / DOMAIN (BUSINESS LOGIC)**

Includes:
* **BINANCE_API_ENGINE**: Fetch data from Binance
* **DATA_PROCESSING_ENGINE**: Transform & validate data
* **INDICATOR_ENGINE**: Calculate MA20, MA50
* **STORAGE_ENGINE**: Database operations
* **USER_ENTITLEMENT**: User access & quotas
* Processing flow & debug cookbook

---

### **TIER C — DELIVERY / SURFACE**

Includes:
* API Contracts / Endpoints
* UI/UX Web / Dashboard
* Admin Tools
* Ops / DevOps / Deployment

---

## **3. `/docs` DIRECTORY STRUCTURE (OFFICIAL STANDARD)**

### **Tier A-B-C Structure (STANDARD OFFICIAL)**

```
docs/
├── 00_ARCHITECTURE_FOUNDATION/        [Tier A: System Laws]
│   ├── 00_00_docs_storage_architecture_lawbook.md
│   ├── 00_01_database_schema_canonical.md
│   ├── 00_02_api_standards_binance.md
│   ├── 00_03_code_conventions_ci4.md
│   ├── 01_DATABASE/                   [Database Schema Folder]
│   │   ├── 00_01_99_index.md
│   │   ├── 00_01_01_btcdatadb_kline_data.md
│   │   ├── 00_01_02_tbl_coin_cryptocurrency_list.md
│   │   ├── 00_01_03_tbl_users_authentication.md
│   │   └── 00_01_04_sql_export.sql
│   └── 00_99_index.md
│
├── 01_ENGINE_DOMAIN/                  [Tier B: Business Engines]
│   ├── 01_BINANCE_API_ENGINE/
│   │   ├── 01_01_01_binance_klines_api.md
│   │   ├── 01_01_02_api_error_handling.md
│   │   └── 01_01_99_index.md
│   │
│   ├── 02_DATA_PROCESSING_ENGINE/
│   │   ├── 01_02_01_duplicate_prevention.md
│   │   ├── 01_02_02_data_validation_rules.md
│   │   └── 01_02_99_index.md
│   │
│   ├── 03_INDICATOR_ENGINE/
│   │   ├── 01_03_01_ma20_calculation_rules.md
│   │   ├── 01_03_02_ma50_calculation_rules.md
│   │   └── 01_03_99_index.md
│   │
│   ├── 04_STORAGE_ENGINE/
│   │   ├── 01_04_01_database_operations.md
│   │   ├── 01_04_02_query_patterns.md
│   │   └── 01_04_99_index.md
│   │
│   ├── 05_USER_ENTITLEMENT/
│   │   ├── 01_05_01_user_tier_definition.md
│   │   ├── 01_05_02_quota_rules.md
│   │   └── 01_05_99_index.md
│   │
│   ├── 01_00_engine_pipeline_canonical_order.md
│   └── 01_99_index.md
│
├── 02_API_CONTRACTS/                  [Tier C: API Specifications]
│   ├── 02_01_01_import_endpoints.md
│   ├── 02_01_02_query_endpoints.md
│   └── 02_99_index.md
│
├── 04_UI_UX/                          [Tier C: Web UI/UX]
│   ├── 04_01_01_dashboard_specs.md
│   └── 04_99_index.md
│
├── 05_ADMIN/                          [Tier C: Admin Tools]
│   ├── 05_01_01_admin_panel_features.md
│   └── 05_99_index.md
│
├── 07_OPS_DEVOPS/                     [Tier C: Deployment & DevOps]
│   ├── 07_01_01_deployment_guide.md
│   ├── 07_01_02_ci_cd_pipeline.md
│   └── 07_99_index.md
│
└── 99_ARCHIVE/                        [Deprecated/Old Documentation]
```

### **Status**: ✅ FINALIZED - No changes planned.

---

## **4. FILE NAMING RULES — LEVEL 1 (LOCKED)**

### **4.1. Standard Format**

`<FOLDER_PREFIX>_<DOC_INDEX>_<DOC_SLUG>.md`

### **Examples (00_ARCHITECTURE_FOUNDATION)**

* `00_00_docs_storage_architecture_lawbook.md`
* `00_01_database_schema_canonical.md`
* `00_02_api_standards_binance.md`
* `00_03_code_conventions_ci4.md`
* `00_99_index.md`

### **4.2. Meaning**

* `00` → Architecture Foundation tier
* `DOC_INDEX` → Logical order
* `DOC_SLUG` → Brief description, no folder name repetition

⛔ **PROHIBITED**
* Repeating folder name in file name
* Adding version in file name (v1, v2…)

---

## **6. OFFICIAL STANDARDIZATION RULES**

### **LEVEL 2 FOLDERS & FILES INSIDE (FINAL – LOCKED)**

### **MANDATORY REFERENCES**

* `00_01_docs_normalization_master_plan_v1.md` (if exists)
* `01_00_engine_pipeline_canonical_order.md`

---

## **6.1. CORE PRINCIPLES (LOCKED)**

* Each level 2 folder **MUST reflect PARENT CONTEXT**
* **NO "bare" folders** (without number prefix)
* **NO flexible interpretation based on intuition**
* GPT / Dev **only needs to read the name** to understand:
  * Which tier it belongs to
  * Which engine it belongs to
  * Its logical position in the pipeline

⛔ Violation = **ARCHITECTURAL BREACH**

---

## **6.2. LEVEL 2 FOLDER DEFINITION (FINALIZED)**

**Level 2 folders** are subdirectories directly inside level 1 folders, used to:
* Group **ONE COMPLETE ENGINE / DOMAIN LOGIC**
* Or **ONE CLUSTER OF RELATED DOCUMENTATION**

### **Valid Examples (Actual 01_ENGINE_DOMAIN Structure)**

```
docs/01_ENGINE_DOMAIN/
├── 01_BINANCE_API_ENGINE/           [Engine 1: Binance API Data Collection]
├── 02_DATA_PROCESSING_ENGINE/       [Engine 2: Data Validation & Transform]
├── 03_INDICATOR_ENGINE/             [Engine 3: Technical Indicators (MA20/MA50)]
├── 04_STORAGE_ENGINE/               [Engine 4: Database Operations]
├── 05_USER_ENTITLEMENT/             [Engine 5: User Tier & Quota]
└── 01_00_engine_pipeline_canonical_order.md
```

**✅ OFFICIAL STANDARD**: 
- Current structure is **OFFICIAL STANDARD**
- DO NOT change folder names (maintain system integrity)
- NO reconciliation needed

---

## **6.3. MANDATORY FORMAT — LEVEL 2 FOLDER NAME**

`<P2>_<ENGINE_NAME>`

| Component | Meaning |
|-----------|---------|
| P2 | Logical order in ENGINE PIPELINE |
| ENGINE_NAME | Engine name, UPPERCASE, snake_case |

### **Correct Examples**

* `01_BINANCE_API_ENGINE`
* `02_DATA_PROCESSING_ENGINE`
* `03_INDICATOR_ENGINE`
* `04_STORAGE_ENGINE`
* `05_USER_ENTITLEMENT`

### **Wrong Examples (PROHIBITED)**

* `BINANCE_API/` ❌ no prefix
* `01_BINANCE/` ❌ truncated name
* `API_ENGINE/` ❌ arbitrary naming

📌 **MANDATORY**

* `P2` **MUST NOT reset**
* `P2` **MUST match canonical pipeline**

---

## **6.4. LINK TO ENGINE PIPELINE (MANDATORY)**

File **`01_00_engine_pipeline_canonical_order.md`** is the **SINGLE SOURCE OF TRUTH** that determines:

| P2 | Engine | Meaning | Official Folder |
|----|--------|---------|-----------------|
| 01 | BINANCE_API_ENGINE | Layer 1: API Data Collection | `01_BINANCE_API_ENGINE/` |
| 02 | DATA_PROCESSING_ENGINE | Layer 2: Data Validation & Transform | `02_DATA_PROCESSING_ENGINE/` |
| 03 | INDICATOR_ENGINE | Layer 3: Technical Indicators | `03_INDICATOR_ENGINE/` |
| 04 | STORAGE_ENGINE | Layer 4: Database Operations | `04_STORAGE_ENGINE/` |
| 05 | USER_ENTITLEMENT | Layer 5: User Tier & Quota | `05_USER_ENTITLEMENT/` |

**STATUS**: ✅ FINALIZED - All folder names are OFFICIAL STANDARD

No changes planned. This structure is LOCKED.

---

## **6.5. FILE NAMING RULES WITHIN LEVEL 2 FOLDERS (FINALIZED)**

### **STANDARD FORMAT**

`<P1>_<P2>_<C1>_<DOC_SLUG>.md`

| Component | Meaning |
|-----------|---------|
| P1 | Level 1 folder prefix (00 or 01, 02, 04, 05, 07) |
| P2 | Level 2 folder prefix (01, 02, 03, 04, 05) |
| C1 | File order within engine/domain |
| DOC_SLUG | Content description |

### **Correct Examples**

Inside `docs/01_ENGINE_DOMAIN/01_BINANCE_API_ENGINE/`:
* `01_01_01_binance_klines_api.md`
* `01_01_02_api_error_handling.md`
* `01_01_99_index.md`

Inside `docs/01_ENGINE_DOMAIN/03_INDICATOR_ENGINE/`:
* `01_03_01_ma20_calculation_rules.md`
* `01_03_02_ma50_calculation_rules.md`
* `01_03_99_index.md`

### **Wrong Examples (PROHIBITED)**

* `binance_api.md` ❌ no prefix
* `01_binance.md` ❌ wrong format
* `01_01_klines.md` ❌ missing P2

---

## **6.6. INDEX FILE — MANDATORY**

Each level 2 folder **MUST HAVE**:

`<P1>_<P2>_99_index.md`

Minimum content:
* List of files
* One-line description for each file
* Internal links to files
* Purpose of engine/domain

⛔ No index = **orphaned folder**

---

## **6.7. LOCKED RULES — NO VIOLATIONS ALLOWED**

* No folders without prefix
* No number resets
* No mixed naming styles
* No invented engines outside pipeline
* No GPT self-interpretation

---

## **6.8. STANDARD REFERENCE ADDRESSES (FINALIZED)**

| File | Role |
|------|------|
| `00_00_docs_storage_architecture_lawbook.md` | Naming laws (this document) |
| `01_00_engine_pipeline_canonical_order.md` | Engine order |
| `00_01_database_schema_canonical.md` | Database schema |
| `00_02_api_standards_binance.md` | API standards |
| `00_03_code_conventions_ci4.md` | Code conventions |

➡️ Conflicts → **prioritize in order above**

---

## **6.9. ARCHITECTURAL COMMITMENT (FINAL)**

* `BINANCE_API_ENGINE` **ALWAYS IS**  
  `docs/01_ENGINE_DOMAIN/01_BINANCE_API_ENGINE/`

* Base file:  
  `01_01_01_binance_klines_api.md`

* `INDICATOR_ENGINE` **ALWAYS IS**  
  `docs/01_ENGINE_DOMAIN/03_INDICATOR_ENGINE/`

* Base file:  
  `01_03_01_ma20_calculation_rules.md`

➡️ If GPT answers with wrong prefix or tier = **VIOLATES LAW**

---

## **8. INDEX FILES (SYSTEM-WIDE)**

Every folder must have `*_99_index.md`

---

## **9. METADATA HEADER (MANDATORY)**

Every `.md` file must have standard header:

```markdown
<!-- filepath: /Users/PhamGiaPhuc/Desktop/ci4/docs/00_ARCHITECTURE_FOUNDATION/00_00_docs_storage_architecture_lawbook.md -->
# Document Title
```

---

## **10. PROCESS FOR ADDING NEW DOCUMENTATION**

1. Determine tier (A/B/C)
2. Choose level 1 folder
3. Choose or create level 2 folder according to pipeline
4. Name file with correct format `P1_P2_C1_slug.md`
5. Update `*_99_index.md` in level 2 folder
6. If it's a LAW → link from `00_99_index.md`

---

## **13. AUDIT & RECONCILIATION STATUS (2026-01-18 - FINALIZED)**

### **Current Status**: ✅ OFFICIAL STANDARD (LOCKED)

**Lawbook has been defined as the official standard for CI4 Crypto project.**

**Key Points**:
- ✅ Tier A (00_ARCHITECTURE_FOUNDATION): 5 core foundation docs + Database folder
- ✅ Tier B (01_ENGINE_DOMAIN): 5 engines + canonical pipeline order
- ✅ Tier C (02, 04, 05, 07): Delivery/Surface layers, ready for expansion

**No reconciliation plan needed** - New structure from start.

---

## **12. FINAL COMMITMENTS**

* ✅ No orphaned documentation
* ✅ No tier misplacement
* ✅ No naming violations (currently FINALIZED)
* ✅ No law changes after finalization (2026-01-18)

**Current folder structure is OFFICIAL and LOCKED** - All folder names and organization are now the standard reference.

---

## **REFERENCE LINKS (IMPLEMENTATION)**

| File | Role | Purpose |
|------|------|---------|
| `docs/00_ARCHITECTURE_FOUNDATION/00_00_docs_storage_architecture_lawbook.md` | **LAW** | Defines architecture & rules (FINALIZED) |
| `.copilot-instructions.md` | **IMPLEMENTATION GUIDE** | Guidelines for AI & Dev to execute laws |
| `docs/01_ENGINE_DOMAIN/01_00_engine_pipeline_canonical_order.md` | **CANONICAL PIPELINE** | Defines engine order & layers |

---

**Last Updated**: January 18, 2026  
**Status**: ✅ FINALIZED & LOCKED
