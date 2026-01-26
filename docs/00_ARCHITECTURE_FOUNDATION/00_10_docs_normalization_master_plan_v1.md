# **DOCS NORMALIZATION MASTER PLAN (v1) — CI4 Crypto Data Analysis**

---

## **1. TARGET DOCUMENTATION STRUCTURE (AFTER NORMALIZATION)**

This is the **standard structure you should migrate toward**, replacing the current scattered documentation.

```
docs/
├── 00_ARCHITECTURE_FOUNDATION/
│   ├── 00_00_docs_storage_architecture_lawbook.md
│   ├── 00_01_database_schema_canonical.md
│   ├── 00_02_api_standards_binance.md
│   ├── 00_03_code_conventions_ci4.md
│   ├── 01_DATABASE/
│   │   ├── 00_01_99_index.md
│   │   ├── 00_01_01_btcdatadb_kline_data.md
│   │   ├── 00_01_02_tbl_coin_cryptocurrency_list.md
│   │   ├── 00_01_03_tbl_users_authentication.md
│   │   └── 00_01_04_sql_export.sql
│   └── 00_99_index.md
│
├── 01_ENGINE_DOMAIN/
│   ├── 01_00_engine_pipeline_canonical_order.md
│   │
│   ├── 01_BINANCE_API_ENGINE/
│   │   ├── 01_01_01_binance_klines_api.md
│   │   ├── 01_01_02_api_error_handling.md
│   │   ├── 01_01_03_time_handling_milliseconds.md
│   │   └── 01_01_99_index.md
│   │
│   ├── 02_DATA_PROCESSING_ENGINE/
│   │   ├── 01_02_01_duplicate_prevention_logic.md
│   │   ├── 01_02_02_data_validation_rules.md
│   │   ├── 01_02_03_date_extraction_from_klines.md
│   │   └── 01_02_99_index.md
│   │
│   ├── 03_INDICATOR_ENGINE/
│   │   ├── 01_03_01_ma20_calculation_rules.md
│   │   ├── 01_03_02_ma50_calculation_rules.md
│   │   ├── 01_03_03_sliding_window_algorithm.md
│   │   ├── 01_03_04_batch_update_optimization.md
│   │   └── 01_03_99_index.md
│   │
│   ├── 04_STORAGE_ENGINE/
│   │   ├── 01_04_01_database_operations.md
│   │   ├── 01_04_02_query_patterns_ci4.md
│   │   ├── 01_04_03_model_methods_reference.md
│   │   └── 01_04_99_index.md
│   │
│   ├── 05_USER_ENTITLEMENT/
│   │   ├── 01_05_01_user_authentication_flow.md
│   │   ├── 01_05_02_role_based_access_control.md
│   │   ├── 01_05_03_session_management.md
│   │   └── 01_05_99_index.md
│   │
│   └── 01_99_index.md
│
├── 02_API_CONTRACTS/
│   ├── 02_01_01_import_endpoints.md
│   ├── 02_01_02_query_endpoints.md
│   ├── 02_01_03_chart_data_endpoints.md
│   ├── 02_01_04_error_codes_taxonomy.md
│   └── 02_99_index.md
│
├── 04_UI_UX/
│   ├── 04_01_01_dashboard_layout.md
│   ├── 04_01_02_candlestick_chart_specs.md
│   ├── 04_01_03_data_table_display.md
│   └── 04_99_index.md
│
├── 05_ADMIN/
│   ├── 05_01_01_admin_panel_features.md
│   ├── 05_01_02_data_import_management.md
│   └── 05_99_index.md
│
├── 07_OPS_DEVOPS/
│   ├── 07_01_01_deployment_guide.md
│   ├── 07_01_02_ci_cd_pipeline.md
│   ├── 07_01_03_performance_optimization.md
│   └── 07_99_index.md
│
└── 99_ARCHIVE/
    └── 2026/
```

**Status**: ✅ TARGET STRUCTURE DEFINED

---

## **2. SPECIFIC MAPPING: CURRENT → STANDARD STRUCTURE**

### **2.1. Existing: `PROJECT_INSTRUCTIONS.md` (ROOT DOCS)**

**Current location**: `/Users/PhamGiaPhuc/Desktop/ci4/docs/PROJECT_INSTRUCTIONS.md`

**Action**: 
- ✅ Keep as-is (it's a comprehensive reference)
- OR split into:
  - Tier A → `00_ARCHITECTURE_FOUNDATION/00_04_project_overview.md`
  - Sector 1 → `01_ENGINE_DOMAIN/01_BINANCE_API_ENGINE/`
  - Sector 2 → `01_ENGINE_DOMAIN/03_INDICATOR_ENGINE/`
  - Sector 3 → `02_API_CONTRACTS/02_01_03_chart_data_endpoints.md`
  - Sector 4 → `05_ADMIN/` or `01_ENGINE_DOMAIN/05_USER_ENTITLEMENT/`
  - Sector 5 → Already in `.copilot-instructions.md` ✅

---

### **2.2. Existing: `.copilot-instructions.md` (ROOT)**

**Current location**: `/Users/PhamGiaPhuc/Desktop/ci4/.copilot-instructions.md`

**Action**:
- ✅ KEEP as implementation guide (bridges Lawbook to AI)
- Link from `00_99_index.md` with note: "Implementation reference"
- No migration needed (it's a meta-instruction file, not documentation)

---

### **2.3. Existing: `00_00_docs_storage_architecture_lawbook.md`**

**Current location**: `/Users/PhamGiaPhuc/Desktop/ci4/docs/00_ARCHITECTURE_FOUNDATION/`

**Action**: ✅ Already in correct place. No change.

---

### **2.4. Missing: Engine-Specific Documentation**

**To be created** (Tier B):

| Engine | Current State | Action |
|--------|---------------|--------|
| **BINANCE_API_ENGINE** | Docs scattered in `C_Database.php` comments | ✅ Extract → `01_01_01_binance_klines_api.md` |
| **DATA_PROCESSING_ENGINE** | Docs scattered in `C_Database.php` comments | ✅ Extract → `01_02_01_duplicate_prevention_logic.md` |
| **INDICATOR_ENGINE** | Docs in `C_Database.php` (MA20, MA50 methods) | ✅ Extract → `01_03_01_ma20_calculation_rules.md` |
| **STORAGE_ENGINE** | Docs in `M_Coin_Data.php` | ✅ Extract → `01_04_01_database_operations.md` |
| **USER_ENTITLEMENT** | Planned (auth module WIP) | ⏳ Create when auth completed |

---

### **2.5. Missing: API Contract Documentation (Tier C)**

**Currently**: No dedicated API spec file

**To be created**:
- `02_01_01_import_endpoints.md` → Binance_Import(), Binance_Daily_Import()
- `02_01_02_query_endpoints.md` → Database display endpoints
- `02_01_03_chart_data_endpoints.md` → Chart data format

---

### **2.6. Missing: Database Schema Documentation (Tier A)**

**Currently**: Schema described in `PROJECT_INSTRUCTIONS.md` only

**To be created**:
- `00_01_01_btcdatadb_kline_data.md` (full schema with constraints)
- `00_01_02_tbl_coin_cryptocurrency_list.md` (coin reference table)
- `00_01_03_tbl_users_authentication.md` (auth table when built)
- `00_01_04_sql_export.sql` (full SQL schema file)

---

## **3. MIGRATION RULES (VERY IMPORTANT)**

### **3.1. Do NOT modify content first**

- Only **move + rename** files
- Content refinement comes **after** structural migration

### **3.2. Rename by new prefix**

Example:

Current: `C_Database.php` method docblocks
→ Extract to: `01_01_01_binance_klines_api.md`

### **3.3. MANDATORY: Every folder needs `XX_99_index.md`**

- Create empty index files first
- Fill descriptions later

---

## **4. IMPLEMENTATION CHECKLIST (IN ORDER)**

### **Phase 1: Create Folder Structure**

- [ ] Create all level-2 folders (already done? verify)
- [ ] Create all `*_99_index.md` files (empty OK for now)
- [ ] Verify folder names match pipeline order (01, 02, 03, 04, 05)

### **Phase 2: Extract Documentation from Code**

- [ ] Extract `C_Database.php` docblocks → `01_ENGINE_DOMAIN/01_BINANCE_API_ENGINE/*.md`
- [ ] Extract `M_Coin_Data.php` docblocks → `01_ENGINE_DOMAIN/04_STORAGE_ENGINE/*.md`
- [ ] Extract time handling logic → `01_ENGINE_DOMAIN/02_DATA_PROCESSING_ENGINE/01_02_03_time_handling.md`

### **Phase 3: Create Database Schema Docs**

- [ ] `00_01_01_btcdatadb_kline_data.md` (fields, types, constraints)
- [ ] `00_01_02_tbl_coin_cryptocurrency_list.md` (coin reference)
- [ ] `00_01_04_sql_export.sql` (full CREATE TABLE statements)

### **Phase 4: Create API Contract Docs**

- [ ] `02_01_01_import_endpoints.md` (Binance_Import endpoints)
- [ ] `02_01_02_query_endpoints.md` (database/chart endpoints)
- [ ] `02_01_04_error_codes_taxonomy.md` (HTTP 500, validation errors)

### **Phase 5: Create Engine Docs**

- [ ] `01_01_02_api_error_handling.md` (cURL failures, API errors)
- [ ] `01_02_02_data_validation_rules.md` (input validation)
- [ ] `01_03_03_sliding_window_algorithm.md` (MA20/MA50 details)
- [ ] `01_04_02_query_patterns_ci4.md` (where(), select(), orderBy() patterns)

### **Phase 6: Populate Index Files**

- [ ] Update all `*_99_index.md` with file lists + descriptions
- [ ] Add cross-references between related docs
- [ ] Link from main index `00_99_index.md` to all law files

### **Phase 7: Sync with Implementation**

- [ ] Update `.copilot-instructions.md` section "Documentation Organization"
- [ ] Add links to new engine docs
- [ ] Verify lawbook references are still accurate

---

## **5. CATEGORIZATION TEMPLATE (FOR EACH DOCUMENT)**

When migrating/creating documents, mark them:

| Attribute | Value | Example |
|-----------|-------|---------|
| **Tier** | A / B / C | Tier B (Business Logic) |
| **Category** | LAW / ENGINE / DELIVERY | ENGINE |
| **Engine** | (if B) | BINANCE_API_ENGINE |
| **Status** | ✅ Complete / 🔄 WIP / ⏳ Planned | ✅ Complete |
| **Priority** | HIGH / MEDIUM / LOW | HIGH |

---

## **6. WHAT GPT WILL DO FOR YOU AFTER NORMALIZATION**

After normalization is complete:

**When you say:**
```
"Write docs for the duplicate prevention logic"
```

**I will respond with:**
```
📁 Folder: docs/01_ENGINE_DOMAIN/02_DATA_PROCESSING_ENGINE/
📄 File: 01_02_01_duplicate_prevention_logic.md
🔢 Next index: 01_02_02
�� Add to index: "Duplicate prevention via composite key (id_coin, date, open_time)"
```

---

## **7. CONCLUSION (VERY IMPORTANT)**

### **Current State**

✅ Your structure is **NOT WRONG**, just **lacking laws**

✅ You have the key components:
- `.copilot-instructions.md` (meta-instructions)
- `00_00_docs_storage_architecture_lawbook.md` (naming rules)
- `PROJECT_INSTRUCTIONS.md` (comprehensive reference)
- `C_Database.php` + `M_Coin_Data.php` (code comments)

### **After Normalization**

✅ Docs = **system with predictable structure**

✅ No more orphaned files

✅ No more tier confusion

✅ Foundation for:
- Scaling to 10+ developers
- Building Flutter mobile app
- Training new team members
- Auto-generating API docs
- Maintaining consistency over years

### **Timeline Estimate**

| Phase | Effort | Time |
|-------|--------|------|
| Folder + Index creation | Low | 1-2 hours |
| Extract from code | Medium | 3-4 hours |
| Write schema docs | Medium | 2-3 hours |
| Create API contracts | Low | 1-2 hours |
| **Total** | **Medium** | **~8-10 hours** |

---

## **NEXT STEPS FOR YOU**

1. **Review** this master plan
2. **Prioritize**: Start with Phase 1 (folder structure)
3. **Execute**: Create folders + empty index files
4. **Verify**: Folder structure matches pipeline order
5. **Proceed**: Phase 2 (extract from code)

---

**Last Updated**: January 18, 2026  
**Status**: ✅ MASTER PLAN COMPLETE  
**Next Review**: After Phase 2 completion
