#!/bin/bash
# bin/deploy.sh — Pull latest code and refresh the environment
# Usage: ./bin/deploy.sh [--skip-migrations] [--skip-seed]
#
# Run from Laravel root. Detects APP_ENV and adjusts automatically.

set -e

SKIP_MIGRATIONS=false
SKIP_SEED=false

for arg in "$@"; do
  case $arg in
    --skip-migrations) SKIP_MIGRATIONS=true ;;
    --skip-seed) SKIP_SEED=true ;;
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

# Seed (only in non-production)
if [ "$SKIP_SEED" = false ] && [ "$APP_ENV_VAL" != "production" ]; then
  echo "[deploy] Skipping seed (use --force-seed to override)"
fi

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

# If Supervisor is installed, restart the managed queue worker (common on this server)
if command -v supervisorctl >/dev/null 2>&1; then
  echo "[deploy] Reloading Supervisor and restarting queue worker..."
  supervisorctl reread || true
  supervisorctl update || true
  # restart a common program name; adjust if your supervisor conf uses a different name
  supervisorctl restart bekkas-queue || true
fi

echo "[deploy] Done. Branch '$BRANCH' deployed."
