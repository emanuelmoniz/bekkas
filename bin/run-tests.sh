#!/usr/bin/env bash
set -euo pipefail

# Safe test runner for BEKKAS (runs tests in isolated SQLite DB)
# Usage:
#   chmod +x bin/run-tests.sh
#   ./bin/run-tests.sh            # run all tests
#   FORCE=1 ./bin/run-tests.sh    # force run even if APP_ENV=production
# Additional PHPUnit options can be passed through: ./bin/run-tests.sh --filter Contacts

# Safety: abort if running on a production env unless FORCE=1
CURRENT_APP_ENV=$(php -r 'echo getenv("APP_ENV");') || true
if [ "${FORCE:-}" != "1" ] && [ "${CURRENT_APP_ENV}" = "production" ]; then
  echo "Refusing to run tests while APP_ENV=production. Set FORCE=1 to override."
  exit 1
fi

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
php artisan migrate --force

echo "Running PHPUnit tests..."
# Pass through any args to artisan test
php artisan test "$@"

# You can uncomment the following line to remove the temporary DB after tests
# rm -f "$TMPDB"

echo "Done. Tests ran using isolated DB: $TMPDB"