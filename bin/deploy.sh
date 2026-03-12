#!/bin/bash
# bin/deploy.sh — Pull latest code and refresh the environment
# Usage: ./bin/deploy.sh [--skip-migrations] [--no-seed|--skip-seed|--force-seed|--seed-no-content|--seed-content]
#
# Seed options (mutually exclusive; default = no seeding):
#   --no-seed          Don't seed anything (default; --skip-seed is an alias)
#   --force-seed       Run full DatabaseSeeder (all seeders, even on production)
#   --seed-no-content  Run all seeders EXCEPT ProductSeeder and ProjectSeeder
#   --seed-content     Run only ProductSeeder and ProjectSeeder
#
# Run from Laravel root. Detects APP_ENV and adjusts automatically.

set -e

SKIP_MIGRATIONS=false
SEED_MODE="none"  # none | force | no-content | content

for arg in "$@"; do
  case $arg in
    --skip-migrations)   SKIP_MIGRATIONS=true ;;
    --no-seed|--skip-seed) SEED_MODE="none" ;;
    --force-seed)        SEED_MODE="force" ;;
    --seed-no-content)   SEED_MODE="no-content" ;;
    --seed-content)      SEED_MODE="content" ;;
  esac
done

# Determine current branch
BRANCH=$(git rev-parse --abbrev-ref HEAD)
echo "[deploy] Branch: $BRANCH"

# Pull latest
git pull origin "$BRANCH"

# Install/update PHP deps
if [ -f ".env" ]; then
  APP_ENV_VAL=$(grep '^APP_ENV=' .env | cut -d '=' -f2)
else
  APP_ENV_VAL="production"
fi

echo "[deploy] APP_ENV: $APP_ENV_VAL"

if [ "$APP_ENV_VAL" = "local" ]; then
  composer install --no-interaction
else
  # Remove stale bootstrap cache before installing to avoid pre-uninstall script
  # crashes when switching from a dev vendor state to a --no-dev install.
  rm -f bootstrap/cache/packages.php bootstrap/cache/services.php
  composer install --no-dev --optimize-autoloader --no-interaction
fi

# Build frontend assets
npm ci
npm run build

# Migrations
if [ "$SKIP_MIGRATIONS" = false ]; then
  php artisan migrate --force
fi

# Seed
case "$SEED_MODE" in
  none)
    echo "[deploy] Seeding skipped (use --force-seed, --seed-no-content, or --seed-content to seed)."
    ;;
  force)
    echo "[deploy] Running full DatabaseSeeder..."
    php artisan db:seed --force
    ;;
  no-content)
    echo "[deploy] Running seeders without content (no ProductSeeder / ProjectSeeder)..."
    php artisan db:seed --class=RoleSeeder --force
    php artisan db:seed --class=TaxSeeder --force
    php artisan db:seed --class=CategorySeeder --force
    php artisan db:seed --class=MaterialSeeder --force
    php artisan db:seed --class=CountrySeeder --force
    php artisan db:seed --class=RegionSeeder --force
    php artisan db:seed --class=LocaleSeeder --force
    php artisan db:seed --class=ShippingTierSeeder --force
    php artisan db:seed --class=TicketCategorySeeder --force
    php artisan db:seed --class=TicketCategoryTranslationSeeder --force
    php artisan db:seed --class=StaticTranslationsSeeder --force
    ;;
  content)
    echo "[deploy] Running content seeders only (ProductSeeder + ProjectSeeder)..."
    php artisan db:seed --class=ProductSeeder --force
    php artisan db:seed --class=ProjectSeeder --force
    ;;
esac

# Storage link
php artisan storage:link 2>/dev/null || true

# Clear & cache based on env
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear

if [ "$APP_ENV_VAL" = "production" ] || [ "$APP_ENV_VAL" = "staging" ]; then
  echo "[deploy] Caching config/routes/views for $APP_ENV_VAL..."
  php artisan config:cache
  php artisan route:cache
  php artisan view:cache
fi

# Signal queue workers to restart gracefully via Laravel cache.
# supervisorctl requires root socket access; php artisan queue:restart avoids that
# by writing a restart timestamp to cache — supervisor then auto-restarts the process.
echo "[deploy] Signalling queue worker to restart (php artisan queue:restart)..."
php artisan queue:restart

echo "[deploy] Done. Branch '$BRANCH' deployed."
