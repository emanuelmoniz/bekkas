#!/usr/bin/env bash
set -euo pipefail

# collect-incident.sh
# Gather logs and environment info to investigate accidental production DB changes.
# Usage: chmod +x bin/collect-incident.sh && ./bin/collect-incident.sh

TIMESTAMP=$(date +%Y%m%d_%H%M%S)
OUTDIR="/tmp/incident_${TIMESTAMP}"
ARCHIVE="/tmp/incident_${TIMESTAMP}.tar.gz"
mkdir -p "$OUTDIR"

echo "Collecting incident diagnostics into $OUTDIR"

# Basic environment
echo "=== Environment variables (selected) ===" > "$OUTDIR/env.txt"
printenv | egrep '^(USER|HOME|SHELL|APP_ENV|DB_CONNECTION|DB_DATABASE|DB_USERNAME|DB_HOST|DB_PORT)' || true >> "$OUTDIR/env.txt"

# Copy local env files (be careful, they contain secrets)
if [ -f .env ]; then
  echo "Copying .env" > "$OUTDIR/env_copy.warn"
  cp .env "$OUTDIR/.env.copy"
fi
if [ -f .env.testing ]; then
  cp .env.testing "$OUTDIR/.env.testing.copy"
fi

# Laravel cache and bootstrap
mkdir -p "$OUTDIR/bootstrap"
ls -la bootstrap > "$OUTDIR/bootstrap/listing.txt" || true
if [ -f bootstrap/cache/config.php ]; then
  stat -c '%y %n' bootstrap/cache/config.php > "$OUTDIR/bootstrap/config_stat.txt" || true
  cp bootstrap/cache/config.php "$OUTDIR/bootstrap/config.php.copy" || true
fi

# Laravel logs (last 1000 lines)
mkdir -p "$OUTDIR/logs"
if [ -f storage/logs/laravel.log ]; then
  tail -n 1000 storage/logs/laravel.log > "$OUTDIR/logs/laravel.log.tail"
fi

# Migration table (if MySQL client present and credentials available)
# Parse DB credentials from .env (fall back to env vars)
DB_CONN=$(grep -m1 '^DB_CONNECTION=' .env 2>/dev/null | cut -d'=' -f2- || true)
DB_CONN=${DB_CONN:-$(printenv DB_CONNECTION || true)}
DB_NAME=$(grep -m1 '^DB_DATABASE=' .env 2>/dev/null | cut -d'=' -f2- || true)
DB_NAME=${DB_NAME:-$(printenv DB_DATABASE || true)}
DB_USER=$(grep -m1 '^DB_USERNAME=' .env 2>/dev/null | cut -d'=' -f2- || true)
DB_USER=${DB_USER:-$(printenv DB_USERNAME || true)}
DB_PASS=$(grep -m1 '^DB_PASSWORD=' .env 2>/dev/null | cut -d'=' -f2- || true)
DB_PASS=${DB_PASS:-$(printenv DB_PASSWORD || true)}
DB_HOST=$(grep -m1 '^DB_HOST=' .env 2>/dev/null | cut -d'=' -f2- || true)
DB_HOST=${DB_HOST:-$(printenv DB_HOST || true)}
DB_PORT=$(grep -m1 '^DB_PORT=' .env 2>/dev/null | cut -d'=' -f2- || true)
DB_PORT=${DB_PORT:-$(printenv DB_PORT || true)}

echo "Detected DB connection: ${DB_CONN}" > "$OUTDIR/db_info.txt"
echo "DB host: ${DB_HOST:-(none)}" >> "$OUTDIR/db_info.txt"
echo "DB name: ${DB_NAME:-(none)}" >> "$OUTDIR/db_info.txt"

if [ "${DB_CONN}" = "mysql" ] && command -v mysql >/dev/null 2>&1; then
  echo "Preparing to query production MySQL to get migrations and table update times..." >> "$OUTDIR/db_info.txt"
  # Use a temporary my.cnf to avoid leaking password on process list
  if [ -n "$DB_USER" ] && [ -n "$DB_PASS" ]; then
    CNF=$(mktemp)
    chmod 600 "$CNF"
    cat > "$CNF" <<EOF
[client]
user=${DB_USER}
password=${DB_PASS}
host=${DB_HOST:-127.0.0.1}
port=${DB_PORT:-3306}
EOF
    # migrations table
    mysql --defaults-extra-file="$CNF" -e "SELECT id, migration, batch FROM migrations ORDER BY id DESC LIMIT 200;" "$DB_NAME" > "$OUTDIR/migrations_table.txt" || true
    # table update times
    mysql --defaults-extra-file="$CNF" -e "SELECT TABLE_NAME, UPDATE_TIME FROM information_schema.tables WHERE table_schema = '${DB_NAME}' ORDER BY UPDATE_TIME DESC LIMIT 200;" > "$OUTDIR/table_update_times.txt" || true
    # recent binary log list (if accessible)
    mysql --defaults-extra-file="$CNF" -e "SHOW BINARY LOGS;" > "$OUTDIR/mysql_binlogs.txt" || true
    rm -f "$CNF"
  else
    echo "No DB credentials found in .env or environment; cannot query MySQL. Provide credentials or run queries manually." >> "$OUTDIR/db_info.txt"
  fi
else
  echo "MySQL client not available or DB_CONNECTION not mysql; skipping MySQL queries." >> "$OUTDIR/db_info.txt"
fi

# Search for recent migrations or artisan invocations in shell histories and logs
mkdir -p "$OUTDIR/commands"
for h in ~/.bash_history /home/*/.bash_history /root/.bash_history; do
  if [ -f "$h" ]; then
    echo "--- $h" >> "$OUTDIR/commands/history_grep.txt"
    grep -Ei 'artisan|migrate|composer|setup' "$h" | tail -n 200 >> "$OUTDIR/commands/history_grep.txt" || true
  fi
done

# Cron and systemd checks
crontab -l > "$OUTDIR/cron_mycrontab.txt" 2>/dev/null || true
sudo crontab -l > "$OUTDIR/cron_root_crontab.txt" 2>/dev/null || true
systemctl list-timers --all > "$OUTDIR/system_timers.txt" 2>/dev/null || true

# Find recent SQL files and dumps (last 7 days)
find / -type f -iname '*.sql' -mtime -7 -ls 2>/dev/null | head -n 200 > "$OUTDIR/recent_sql_files.txt" || true

# Package everything
tar -czf "$ARCHIVE" -C /tmp "incident_${TIMESTAMP}"

echo "Done. Archive created: $ARCHIVE"
ls -lh "$ARCHIVE"

echo "Please send the archive (or the files in $OUTDIR) for analysis. Do NOT run any migrations until we determine the cause."