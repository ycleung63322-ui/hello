#!/usr/bin/env bash
set -euo pipefail

ROOT="$(cd "$(dirname "$0")/.." && pwd)"
SQL_FILE="$ROOT/sql/schema.sql"

mysql -u root < "$SQL_FILE"

# Ensure app user exists for local demos
mysql -u root <<'SQL'
CREATE USER IF NOT EXISTS 'member_app'@'localhost' IDENTIFIED BY 'member_secret';
GRANT ALL PRIVILEGES ON member_system.* TO 'member_app'@'localhost';
FLUSH PRIVILEGES;
SQL

echo "Database ready: member_system"
