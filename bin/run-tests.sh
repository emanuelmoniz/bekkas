#!/usr/bin/env bash
set -euo pipefail

# Safe test runner for BEKKAS (runs tests in isolated SQLite DB)
# Usage:
#   chmod +x bin/run-tests.sh
#   ./bin/run-tests.sh            # run all tests
#   FORCE=1 ./bin/run-tests.sh    # force run even if APP_ENV=production
# Additional PHPUnit options can be passed through: ./bin/run-tests.sh --filter Contacts

# Safety: abort if running on a production env unless FORCE=1
<<<<<<< HEAD
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

=======
CURRENT_APP_ENV=$(php -r 'echo getenv("APP_ENV");') || true
if [ "${FORCE:-}" != "1" ] && [ "${CURRENT_APP_ENV}" = "production" ]; then
  echo "Refusing to run tests while APP_ENV=production. Set FORCE=1 to override."
  exit 1
fi

>>>>>>> f45c9da9ff11b3a7fedeb8aeee2e295da1e824ad
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

export APP_ENV=testing
export DB_CONNECTION=sqlite
export DB_DATABASE="$TMPDB"
export MAIL_MAILER=array
export CACHE_DRIVER=array
export SESSION_DRIVER=array

echo "Running migrations on test database..."
<<<<<<< HEAD
# Run migrations explicitly against the isolated sqlite DB to avoid touching other connections
DB_CONNECTION=sqlite DB_DATABASE="$TMPDB" php artisan migrate --force --database=sqlite

echo "Running PHPUnit tests..."
# Pass through any args to artisan test, also ensure the test runner uses the isolated DB
DB_CONNECTION=sqlite DB_DATABASE="$TMPDB" php artisan test "$@"
=======
php artisan migrate --force

echo "Running PHPUnit tests..."
# Pass through any args to artisan test
php artisan test "$@"
>>>>>>> f45c9da9ff11b3a7fedeb8aeee2e295da1e824ad

# You can uncomment the following line to remove the temporary DB after tests
# rm -f "$TMPDB"

echo "Done. Tests ran using isolated DB: $TMPDB"