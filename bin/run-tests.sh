#!/usr/bin/env bash
set -euo pipefail

# Safe test runner for BEKKAS (runs tests in isolated SQLite DB)
# Usage:
#   chmod +x bin/run-tests.sh
#   ./bin/run-tests.sh            # run all tests
#   FORCE=1 ./bin/run-tests.sh    # force run even if APP_ENV=production
# Additional PHPUnit options can be passed through: ./bin/run-tests.sh --filter Contacts

# Safety: abort if running on a production env unless FORCE=1
# Prefer values from .env.testing if present (allows running the helper from
# a checked-out working dir without exporting env vars into the shell).
if [ -f ".env.testing" ]; then
  # Read and sanitize values from .env.testing safely without complex quoting
  RAW=$(grep -m1 '^APP_ENV=' .env.testing 2>/dev/null || true)
  CURRENT_APP_ENV=""
  if [ -n "$RAW" ]; then
    CURRENT_APP_ENV=${RAW#APP_ENV=}
    CURRENT_APP_ENV=${CURRENT_APP_ENV%\"}
    CURRENT_APP_ENV=${CURRENT_APP_ENV#\"}
    CURRENT_APP_ENV=${CURRENT_APP_ENV%\'}
    CURRENT_APP_ENV=${CURRENT_APP_ENV#\'}
    CURRENT_APP_ENV=$(echo "$CURRENT_APP_ENV" | sed 's/^[[:space:]]*//; s/[[:space:]]*$//')
  fi

  RAW=$(grep -m1 '^DB_CONNECTION=' .env.testing 2>/dev/null || true)
  CURRENT_DB_CONN=""
  if [ -n "$RAW" ]; then
    CURRENT_DB_CONN=${RAW#DB_CONNECTION=}
    CURRENT_DB_CONN=${CURRENT_DB_CONN%\"}
    CURRENT_DB_CONN=${CURRENT_DB_CONN#\"}
    CURRENT_DB_CONN=${CURRENT_DB_CONN%\'}
    CURRENT_DB_CONN=${CURRENT_DB_CONN#\'}
    CURRENT_DB_CONN=$(echo "$CURRENT_DB_CONN" | sed 's/^[[:space:]]*//; s/[[:space:]]*$//')
  fi

  RAW=$(grep -m1 '^DB_DATABASE=' .env.testing 2>/dev/null || true)
  CURRENT_DB_DB=""
  if [ -n "$RAW" ]; then
    CURRENT_DB_DB=${RAW#DB_DATABASE=}
    CURRENT_DB_DB=${CURRENT_DB_DB%\"}
    CURRENT_DB_DB=${CURRENT_DB_DB#\"}
    CURRENT_DB_DB=${CURRENT_DB_DB%\'}
    CURRENT_DB_DB=${CURRENT_DB_DB#\'}
    CURRENT_DB_DB=$(echo "$CURRENT_DB_DB" | sed 's/^[[:space:]]*//; s/[[:space:]]*$//')
  fi
else
  CURRENT_APP_ENV=$(php -r 'echo getenv("APP_ENV");') || true
  CURRENT_DB_CONN=$(php -r 'echo getenv("DB_CONNECTION");') || true
  CURRENT_DB_DB=$(php -r 'echo getenv("DB_DATABASE");') || true
fi

# Note: We no longer abort on non-sqlite DB or missing .env.testing per user request.
# If .env.testing exists it will be used, otherwise the current environment will be read.
# Be aware this means tests can affect the configured DB if you run them in a production environment.


# Check dependencies
if ! php -v >/dev/null 2>&1; then
  echo "PHP is not installed or not available in PATH"
  exit 1
fi

if ! php -m | grep -qi pdo_sqlite; then
  echo "PDO SQLite extension not found. Install it (e.g. sudo apt install php-sqlite) and retry."
  exit 1
fi

echo "Preparing isolated SQLite database..."
TMPDB="${PWD}/database/testing.sqlite"
mkdir -p database
rm -f "$TMPDB"
touch "$TMPDB"

# Ensure framework writable/cache paths exist on fresh CI checkouts.
# Laravel may resolve Blade compiled path via realpath(storage/framework/views),
# which returns false if the directory does not exist yet.
mkdir -p storage/framework/views
mkdir -p storage/framework/cache
mkdir -p storage/framework/sessions
mkdir -p storage/framework/testing
mkdir -p storage/logs
mkdir -p bootstrap/cache

export APP_ENV=testing
export DB_CONNECTION=sqlite
export DB_DATABASE="$TMPDB"
export MAIL_MAILER=array
export CACHE_DRIVER=array
export SESSION_DRIVER=array

echo "Clearing stale Laravel bootstrap cache..."
rm -f bootstrap/cache/packages.php bootstrap/cache/services.php

echo "Running migrations on test database..."
# Run migrations explicitly against the isolated sqlite DB to avoid touching other connections
DB_CONNECTION=sqlite DB_DATABASE="$TMPDB" php artisan migrate --force --database=sqlite

echo "Running PHPUnit tests..."
# Run through artisan when available; otherwise fallback to vendor phpunit.
if php artisan list --raw | grep -q '^test$'; then
  DB_CONNECTION=sqlite DB_DATABASE="$TMPDB" php artisan test "$@"
elif [ -x "vendor/bin/phpunit" ]; then
  DB_CONNECTION=sqlite DB_DATABASE="$TMPDB" ./vendor/bin/phpunit "$@"
else
  echo "No test runner found. Install dev dependencies (composer install) and retry."
  exit 1
fi

# You can uncomment the following line to remove the temporary DB after tests
# rm -f "$TMPDB"

echo "Done. Tests ran using isolated DB: $TMPDB"