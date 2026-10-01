# MemberHub — PHP + MySQL 會員系統

簡單的會員系統示範，使用 **PHP 8.3** 與 **MySQL / MariaDB**。

## 功能

- 會員註冊（姓名、Email、密碼）
- 會員登入 / 登出
- 會員中心
- 個人資料更新
- CSRF 防護、密碼雜湊（`password_hash`）、PDO 預備語句

## 需求

- PHP 8.3+（需啟用 `pdo_mysql`、`mbstring`）
- MySQL 8+ 或 MariaDB 10.11+

## 快速開始

### 1. 建立資料庫

```bash
chmod +x scripts/setup-db.sh
./scripts/setup-db.sh
```

或手動執行：

```bash
mysql -u root < sql/schema.sql
```

預設連線設定（可在 `config/config.php` 或環境變數調整）：

| 變數 | 預設值 |
|------|--------|
| `DB_HOST` | `127.0.0.1` |
| `DB_PORT` | `3306` |
| `DB_NAME` | `member_system` |
| `DB_USER` | `member_app` |
| `DB_PASS` | `member_secret` |

### 2. 啟動內建伺服器

```bash
php -S 127.0.0.1:8080 -t public
```

瀏覽器開啟：<http://127.0.0.1:8080>

## 專案結構

```
config/          # 設定與 PDO 連線
includes/        # 驗證、輔助函式、版面
public/          # 對外網頁入口
sql/schema.sql   # 資料表結構
scripts/         # 安裝腳本
```

## 安全性說明

- 密碼以 `PASSWORD_DEFAULT` 雜湊儲存
- 所有 SQL 使用 PDO prepared statements
- Session 啟用 `httponly`、`SameSite=Lax`、strict mode
- 表單含 CSRF token
