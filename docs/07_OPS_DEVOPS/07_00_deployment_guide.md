# Chapter 07 — Deployment Guide

**Status:** ACTIVE
**Last Updated:** 2026-04-10 (r2 — thêm SSH access, cập nhật deploy steps)
**Framework:** CodeIgniter 4 / PHP 8.1+
**Scope:** Quy trình deploy lên production

---

## 1. Thông tin Production

| Item | Giá trị |
|------|---------|
| URL | https://giaphuc.thuytrieu.vn/public |
| Server IP | `103.77.160.104` |
| Web Server | LiteSpeed + CyberPanel |
| PHP Version | 8.1+ |
| Database | MySQL (MariaDB) |
| DB Name | `thuy_giaphuc` |
| SSL | HTTPS (bắt buộc) |

### SSH Access

| Item | Giá trị |
|------|---------|
| Host | `103.77.160.104` |
| Port | `2229` |
| User | `giaphuc_dev` |
| Auth | SSH Key (ed25519) |
| Key file | `~/.ssh/giaphuc_dev_ed25519` |
| Home dir | `/home/thuytrieu.vn/giaphuc` |
| Website root | `/home/thuytrieu.vn/giaphuc/public` |

```bash
# Kết nối SSH
ssh -p 2229 -i ~/.ssh/giaphuc_dev_ed25519 giaphuc_dev@103.77.160.104
```

### Authorized Public Keys

| Machine | Fingerprint | Added |
|---------|-------------|-------|
| MacBook (PhamGiaPhuc) | `SHA256:FIGCuj/xVghg3InRUXiXHz/ECgyB6v3RiR6P9nr4oUw` | 2026-04-29 |

> Full public keys được lưu ngoài repo (password manager / secure note).

> **Lưu ý:** User `giaphuc_dev` chỉ có quyền trong thư mục `/home/thuytrieu.vn/giaphuc`, không có quyền root.

---

## 2. Quy trình Deploy

### Pre-deploy Checklist

```
□ Code đã được review
□ Tất cả test pass (php spark test)
□ Không có credentials trong source code
□ .env đúng cho production (CI_ENVIRONMENT = production)
□ Migration đã sẵn sàng (nếu có thay đổi schema)
```

### Deploy Steps

**Cách 1: Tự động qua GitHub Actions (FTP Deploy)**
- Push code lên branch `main` → GitHub Actions tự động FTP upload lên server
- Config: `.github/workflows/deploy.yml`
- Credentials: GitHub Secrets (`FTP_SERVER`, `FTP_USERNAME`, `FTP_PASSWORD`)

**Cách 2: Thủ công qua SSH**
```bash
# 1. Push code lên repository
git push origin main

# 2. SSH vào server
ssh -p 2229 -i ~/.ssh/giaphuc_dev_ed25519 giaphuc_dev@103.77.160.104

# 3. Pull code mới
git pull origin main

# 4. Cài dependencies (nếu cần)
composer install --no-dev --optimize-autoloader

# 5. Chạy migration (nếu có)
php spark migrate

# 6. Verify: truy cập https://giaphuc.thuytrieu.vn/public
```

---

## 3. Environment Configuration

### Production `.env`

```ini
CI_ENVIRONMENT = production

app.baseURL = 'https://giaphuc.thuytrieu.vn/public'

database.default.hostname = localhost
database.default.database = thuy_giaphuc
database.default.username = <production_user>
database.default.password = <production_password>
database.default.DBDriver = MySQLi
```

### Quy tắc Environment

```
✅ ĐÚNG: CI_ENVIRONMENT = production trên server
✅ ĐÚNG: Debug TẮT trên production
✅ ĐÚNG: HTTPS enforced

❌ SAI: CI_ENVIRONMENT = development trên production
❌ SAI: Hiển thị error details cho user
❌ SAI: HTTP (không SSL) trên production
```

---

## 4. Post-deploy Verification

| Kiểm tra | Cách kiểm | Expected |
|----------|-----------|----------|
| Homepage | GET / | 200 OK |
| Login page | GET /login | Form hiển thị |
| Dashboard | Login + GET /dashboard | Chart hiển thị |
| Error logs | `tail -f writable/logs/log-*.log` | Không có critical error |

---

## 5. Rollback

Nếu deploy gây lỗi:

```bash
# Rollback code
git log --oneline -5        # Xem commit history
git revert <commit-hash>    # Revert commit lỗi
git push origin main

# Rollback migration (nếu cần)
php spark migrate:rollback
```

---

## Liên kết

- [Architecture Foundation](../00_ARCHITECTURE_FOUNDATION/00_99_index.md)
- [Project Instructions](../PROJECT%20INTRUCTIONS.md)
