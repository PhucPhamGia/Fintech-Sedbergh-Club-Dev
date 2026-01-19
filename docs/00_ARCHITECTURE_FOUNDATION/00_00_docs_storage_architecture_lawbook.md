# Docs Storage Architecture & Naming Lawbook (v1)

---

## **1. MỤC TIÊU TÀI LIỆU**

Tài liệu này định nghĩa **LUẬT KIẾN TRÚC LƯU TRỮ**, **CHUẨN HOÁ TÀI LIỆU** và **QUY TẮC ĐẶT TÊN FILE / THƯ MỤC** cho toàn bộ dự án CI4 Crypto Data Analysis.

### **Mục tiêu chính**

* Tránh loạn tài liệu khi dự án mở rộng (API, Models, Engines, UI…)

* Giúp **GPT và Team Dev** luôn xác định đúng:
  * Tài liệu đặt ở đâu
  * Đặt tên file thế nào
  * Phân biệt rõ **LUẬT – ENGINE – DELIVERY**

* Cho phép mở rộng **nhiều năm** mà **không phá cấu trúc**

⛔ Mọi vi phạm tài liệu này được xem là **PHÁ KIẾN TRÚC**.

---

## **1.5. LIÊN KẾT VỚI .COPILOT-INSTRUCTIONS.MD (TRIỂN KHAI)**

**Quy tắc trong Lawbook này được triển khai thông qua**:

📄 **File**: `/Users/PhamGiaPhuc/Desktop/ci4/.copilot-instructions.md`

**Phần liên quan**: 
- Section "📂 **Documentation Organization & Naming Rules**"
- Tham chiếu đầy đủ đến Lawbook
- Hướng dẫn cho AI coding agent

**Mối quan hệ**:
- **Lawbook** = Định nghĩa luật kiến trúc (CÓ HIỆU LỰC TOÀN DỰ ÁN)
- **copilot-instructions** = Hướng dẫn triển khai cho AI + Dev
- Cả hai **PHẢI ĐỒNG BỘ**: Nếu thay đổi Lawbook → cập nhật copilot-instructions

---

## **2. LUẬT NỀN TẢNG — TÀI LIỆU PHẢI PHÂN TẦNG**

Mỗi tài liệu **bắt buộc** thuộc **DUY NHẤT MỘT TẦNG**.

### **TẦNG A — ARCHITECTURE / FOUNDATION (BẤT BIẾN)**

Bao gồm:
* Luật kiến trúc (lawbook này)
* Nguyên tắc hệ thống
* Database schema canonical
* API standards
* Code conventions CI4
* Naming rules

✅ Dùng để:
* Huấn luyện GPT
* Onboard Dev
* Review kiến trúc
* Ngăn technical debt

---

### **TẦNG B — ENGINE / DOMAIN (NGHIỆP VỤ)**

Bao gồm:
* **BINANCE_API_ENGINE**: Lấy dữ liệu từ Binance
* **DATA_PROCESSING_ENGINE**: Transform & validate data
* **INDICATOR_ENGINE**: Tính toán MA20, MA50
* **STORAGE_ENGINE**: Database operations
* **USER_ENTITLEMENT**: User access & quotas
* Flow xử lý & debug cookbook

---

### **TẦNG C — DELIVERY / SURFACE**

Bao gồm:
* API Contracts / Endpoints
* UI/UX Web / Dashboard
* Admin Tools
* Ops / DevOps / Deployment

---

## **3. CẤU TRÚC THƯ MỤC `/docs` (CHỐT CHÍNH THỨC)**

### **Cấu trúc Tier A-B-C (STANDARD OFFICIAL)**

```
docs/
├── 00_ARCHITECTURE_FOUNDATION/        [Tier A: System Laws]
│   ├── 00_00_docs_storage_architecture_lawbook.md
│   ├── 00_01_database_schema_canonical.md
│   ├── 00_02_api_standards_binance.md
│   ├── 00_03_code_conventions_ci4.md
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

### **Status**: ✅ CHỐT TOÀN BỘ - No changes planned.

---

## **4. QUY TẮC ĐẶT TÊN FILE — CẤP 1 (ĐÃ CHỐT)**

### **4.1. Format chuẩn**

`<FOLDER_PREFIX>_<DOC_INDEX>_<DOC_SLUG>.md`

### **Ví dụ (00_ARCHITECTURE_FOUNDATION)**

* `00_00_docs_storage_architecture_lawbook.md`
* `00_01_database_schema_canonical.md`
* `00_02_api_standards_binance.md`
* `00_03_code_conventions_ci4.md`
* `00_99_index.md`

### **4.2. Ý nghĩa**

* `00` → tầng Architecture Foundation
* `DOC_INDEX` → thứ tự logic
* `DOC_SLUG` → mô tả ngắn, không trùng tên folder

⛔ **CẤM**
* Lặp lại tên thư mục trong tên file
* Đặt version trong tên file (v1, v2…)

---

## **6. QUY TẮC CHUẨN HOÁ CHÍNH THỨC**

### **FOLDER CẤP 2 & FILE BÊN TRONG (FINAL – LOCKED)**

### **Reference BẮT BUỘC**

* `00_10_docs_normalization_master_plan_v1.md` (nếu có)
* `01_00_engine_pipeline_canonical_order.md`

---

## **6.1. NGUYÊN TẮC GỐC (KHÓA)**

* Mỗi folder cấp 2 **PHẢI phản ánh NGỮ CẢNH CHA**
* **KHÔNG tồn tại** folder "tên trần" (không prefix số)
* **KHÔNG suy luận linh hoạt theo cảm tính**
* GPT / Dev **chỉ cần đọc tên** là hiểu:
  * Thuộc tầng nào
  * Thuộc engine nào
  * Vị trí logic trong pipeline

⛔ Vi phạm = **PHÁ KIẾN TRÚC**

---

## **6.2. ĐỊNH NGHĨA FOLDER CẤP 2 (CHỐT)**

**Folder cấp 2** là thư mục con trực tiếp trong folder cấp 1, dùng để:
* Gom **MỘT ENGINE / DOMAIN LOGIC HOÀN CHỈNH**
* Hoặc **MỘT CỤM TÀI LIỆU CHUNG NGỮ CẢNH**

### **Ví dụ hợp lệ (Cấu trúc thực tế 01_ENGINE_DOMAIN)**

```
docs/01_ENGINE_DOMAIN/
├── 01_BINANCE_API_ENGINE/           [Engine 1: Binance API Data Collection]
├── 02_DATA_PROCESSING_ENGINE/       [Engine 2: Data Validation & Transform]
├── 03_INDICATOR_ENGINE/             [Engine 3: Technical Indicators (MA20/MA50)]
├── 04_STORAGE_ENGINE/               [Engine 4: Database Operations]
├── 05_USER_ENTITLEMENT/             [Engine 5: User Tier & Quota]
└── 01_00_engine_pipeline_canonical_order.md
```

**✅ CHỐT CHÍNH THỨC**: 
- Cấu trúc hiện tại là **OFFICIAL STANDARD**
- KHÔNG thay đổi folder names (giữ nguyên hệ thống)
- KHÔNG reconciliation needed

---

## **6.3. FORMAT BẮT BUỘC — TÊN FOLDER CẤP 2**

`<P2>_<ENGINE_NAME>`

| Thành phần | Ý nghĩa |
|-----------|---------|
| P2 | Thứ tự logic trong ENGINE PIPELINE |
| ENGINE_NAME | Tên engine, HOA, snake_case |

### **Ví dụ ĐÚNG**

* `01_BINANCE_API_ENGINE`
* `02_DATA_PROCESSING_ENGINE`
* `03_INDICATOR_ENGINE`
* `04_STORAGE_ENGINE`
* `05_USER_ENTITLEMENT`

### **Ví dụ SAI (CẤM)**

* `BINANCE_API/` ❌ không prefix
* `01_BINANCE/` ❌ cắt ngắn tên
* `API_ENGINE/` ❌ cảm tính

📌 **BẮT BUỘC**

* `P2` **KHÔNG reset**
* `P2` **PHẢI khớp pipeline canonical**

---

## **6.4. LIÊN KẾT VỚI ENGINE PIPELINE (BẮT BUỘC)**

File **`01_00_engine_pipeline_canonical_order.md`** là **NGUỒN DUY NHẤT** quyết định:

| P2 | Engine | Ý nghĩa | Folder Chính Thức |
|----|--------|---------|-------------------|
| 01 | BINANCE_API_ENGINE | Layer 1: API Data Collection | `01_BINANCE_API_ENGINE/` |
| 02 | DATA_PROCESSING_ENGINE | Layer 2: Data Validation & Transform | `02_DATA_PROCESSING_ENGINE/` |
| 03 | INDICATOR_ENGINE | Layer 3: Technical Indicators | `03_INDICATOR_ENGINE/` |
| 04 | STORAGE_ENGINE | Layer 4: Database Operations | `04_STORAGE_ENGINE/` |
| 05 | USER_ENTITLEMENT | Layer 5: User Tier & Quota | `05_USER_ENTITLEMENT/` |

**STATUS**: ✅ CHỐT - All folder names are OFFICIAL STANDARD

No changes planned. This structure is LOCKED.

---

## **6.5. QUY TẮC ĐẶT TÊN FILE TRONG FOLDER CẤP 2 (CHỐT)**

### **FORMAT CHUẨN (DÙNG CÁCH B)**

`<P1>_<P2>_<C1>_<DOC_SLUG>.md`

| Thành phần | Ý nghĩa |
|-----------|---------|
| P1 | Prefix folder cấp 1 (00 hoặc 01, 02, 04, 05, 07) |
| P2 | Prefix folder cấp 2 (01, 02, 03, 04, 05) |
| C1 | Thứ tự file trong engine/domain |
| DOC_SLUG | Mô tả nội dung |

### **Ví dụ ĐÚNG**

Trong `docs/01_ENGINE_DOMAIN/01_BINANCE_API_ENGINE/`:
* `01_01_01_binance_klines_api.md`
* `01_01_02_api_error_handling.md`
* `01_01_99_index.md`

Trong `docs/01_ENGINE_DOMAIN/03_INDICATOR_ENGINE/`:
* `01_03_01_ma20_calculation_rules.md`
* `01_03_02_ma50_calculation_rules.md`
* `01_03_99_index.md`

### **Ví dụ SAI (CẤM)**

* `binance_api.md` ❌ không prefix
* `01_binance.md` ❌ sai format
* `01_01_klines.md` ❌ thiếu P2

---

## **6.6. INDEX FILE — BẮT BUỘC**

Mỗi folder cấp 2 **PHẢI CÓ**:

`<P1>_<P2>_99_index.md`

Nội dung tối thiểu:
* Danh sách file
* 1 dòng mô tả cho từng file
* Link nội bộ tới các file
* Mục đích của engine/domain

⛔ Không có index = **folder vô chủ**

---

## **6.7. QUY TẮC KHÓA — KHÔNG ĐƯỢC VI PHẠM**

* Không folder không prefix
* Không reset số
* Không trộn kiểu naming
* Không suy engine ngoài pipeline
* Không để GPT tự đoán

---

## **6.8. ĐỊA CHỈ TRA CỨU CHUẨN (CHỐT)**

| File | Vai trò |
|------|---------|
| `00_00_docs_storage_architecture_lawbook.md` | Luật naming (tài liệu này) |
| `01_00_engine_pipeline_canonical_order.md` | Thứ tự engine |
| `00_01_database_schema_canonical.md` | Schema database |
| `00_02_api_standards_binance.md` | API standards |
| `00_03_code_conventions_ci4.md` | Code conventions |

➡️ Mâu thuẫn → **ưu tiên theo thứ tự trên**

---

## **6.9. CAM KẾT KIẾN TRÚC (FINAL)**

* `BINANCE_API_ENGINE` **LUÔN LÀ**  
  `docs/01_ENGINE_DOMAIN/01_BINANCE_API_ENGINE/`

* File gốc:  
  `01_01_01_binance_klines_api.md`

* `INDICATOR_ENGINE` **LUÔN LÀ**  
  `docs/01_ENGINE_DOMAIN/03_INDICATOR_ENGINE/`

* File gốc:  
  `01_03_01_ma20_calculation_rules.md`

➡️ GPT trả lời sai prefix hoặc tầng = **SAI LUẬT**

---

## **8. INDEX FILE (TOÀN HỆ)**

Mỗi folder đều phải có `*_99_index.md`

---

## **9. METADATA HEADER (BẮT BUỘC)**

Mọi file `.md` phải có header chuẩn:

```markdown
<!-- filepath: /Users/PhamGiaPhuc/Desktop/ci4/docs/00_ARCHITECTURE_FOUNDATION/00_00_docs_storage_architecture_lawbook.md -->
# Document Title
```

---

## **10. QUY TRÌNH THÊM TÀI LIỆU MỚI**

1. Xác định tầng (A/B/C)
2. Chọn folder cấp 1
3. Chọn hoặc tạo folder cấp 2 theo pipeline
4. Đặt tên file đúng format `P1_P2_C1_slug.md`
5. Update `*_99_index.md` của folder cấp 2
6. Nếu là LUẬT → link từ `00_99_index.md`

---

## **13. AUDIT & RECONCILIATION STATUS (2026-01-18 - CHỐT)**

### **Current Status**: ✅ OFFICIAL STANDARD (LOCKED)

**Lawbook has been defined as the official standard for CI4 Crypto project.**

**Key Points**:
- ✅ Tier A (00_ARCHITECTURE_FOUNDATION): 5 core foundation docs
- ✅ Tier B (01_ENGINE_DOMAIN): 5 engines + canonical pipeline order
- ✅ Tier C (02, 04, 05, 07): Delivery/Surface layers, ready for expansion

**No reconciliation plan needed** - New structure from start.

---

## **12. CAM KẾT CUỐI**

* ✅ Không tài liệu vô chủ
* ✅ Không sai tầng
* ✅ Không phá naming (hiện tại là CHỐT)
* ✅ Không sửa luật sau khi chốt (2026-01-18)

**Current folder structure is OFFICIAL and LOCKED** - All folder names and organization are now the standard reference.

---

## **LIÊN KẾT THAM CHIẾU (IMPLEMENTATION)**

| File | Vai trò | Mục đích |
|------|---------|---------|
| `docs/00_ARCHITECTURE_FOUNDATION/00_00_docs_storage_architecture_lawbook.md` | **LUẬT** | Định nghĩa kiến trúc & quy tắc (CHỐT) |
| `.copilot-instructions.md` | **HƯỚNG DẪN TRIỂN KHAI** | Hướng dẫn AI & Dev thực thi luật |
| `docs/01_ENGINE_DOMAIN/01_00_engine_pipeline_canonical_order.md` | **PIPELINE CANONICAL** | Xác định thứ tự engine & layer |

---

**Last Updated**: January 18, 2026  
**Status**: ✅ CHỐT & LOCKED
