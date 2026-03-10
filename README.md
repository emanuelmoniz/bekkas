# BEKKAS - 3D Printing Studio (Laravel)

A modern e-commerce and support site for a 3D printing studio built with Laravel 12, Vite, Tailwind CSS and Alpine.js.

---

## 🚀 Quickstart

**Requirements:**
- PHP 8.4+
- Composer
- Node 20.19.0+ (or >=22.12.0) / npm
- MySQL (or a supported DB)

> Use the project-pinned Node version from `.nvmrc` (`nvm install && nvm use`).

**Local setup (typical):**

1. Clone the repository
   - git clone <repo> && cd public_html
2. Install PHP dependencies
   - composer install
3. Copy example env and generate an app key
   - cp .env.example .env
   - php artisan key:generate
4. Configure `.env` (DB, Mail, RECAPTCHA keys, etc.)
5. Run database migrations and seeders
   - php artisan migrate --seed
6. Build frontend assets
   - npm install
   - npm run build
7. Create storage symlink
   - php artisan storage:link
8. Start a local server
   - php artisan serve

> Tip: `composer setup` is available (runs migrations, npm install + build) as a convenience script.

---

## 🧭 Project overview

Key components:
- Laravel 12 (PHP ^8.4)
- Frontend: Vite + Tailwind + Alpine.js
- DB-driven static translations with `t()` helper (cached)
- Features: Products catalog, Cart & Checkout, Orders (client + admin), Shipping Tiers, Tickets (support), Favorites, Admin area
- Storage: a `private` local disk for attachments and `public` disk for media served via `storage:link`

---

## ⚙️ Configuration (`.env`)

Important env variables to set in your `.env` (see `.env.example`):
- APP_URL, APP_ENV, APP_KEY
- DB_CONNECTION, DB_HOST, DB_DATABASE, DB_USERNAME, DB_PASSWORD
- MAIL_MAILER, MAIL_HOST, MAIL_PORT, MAIL_USERNAME, MAIL_PASSWORD, MAIL_ENCRYPTION
- RECAPTCHA_SITE_KEY, RECAPTCHA_SECRET_KEY (used on contact forms and registration)
- FILESYSTEM_DISK (local or s3) + AWS_* vars if using S3

Security notes: never commit real secrets to the repository. Use environment or vaults in CI/CD.

---

<!-- Tests & CI intentionally removed from top-level README until the test suite is stable. See `TESTING.md` for instructions to run tests safely on your server and notes about CI. -->


---

## 📧 Email & queue workers (server notes)

Emails (order notifications, contact form, tickets) are dispatched as queued jobs. The queue driver must be set to `database` and a persistent worker must be running to deliver them.

### `.env` requirements

```dotenv
QUEUE_CONNECTION=database
MAIL_MAILER=smtp
MAIL_HOST=email-smtp.eu-west-3.amazonaws.com
MAIL_PORT=587
MAIL_ENCRYPTION=tls
MAIL_USERNAME=<AWS SES SMTP username>
MAIL_PASSWORD=<AWS SES SMTP password>
MAIL_FROM_ADDRESS=no-reply@bekkas.pt
MAIL_FROM_NAME="${APP_NAME}"
```

> **Do not** duplicate `QUEUE_CONNECTION` in `.env`. If both `sync` and `database` appear, only the last one takes effect and this causes confusion — keep exactly one entry.

### Supervisor setup (production server)

Each environment has its own Supervisor program defined in `/etc/supervisor/conf.d/`. Current programs:

| Environment | Domain | Supervisor program |
|---|---|---|
| Production | bekkas.pt | `bekkas-queue` |
| Staging | tes.bekkas.pt | `tes-bekkas-queue` |
| Development | dev.bekkas.pt | `dev-bekkas-queue` |

Template for a new environment (run as root):

```bash
cat > /etc/supervisor/conf.d/<name>-queue.conf << 'EOF'
[program:<name>-queue]
process_name=%(program_name)s
command=php /home/bekkas/web/<domain>/public_html/artisan queue:listen database --sleep=3 --tries=3
autostart=true
autorestart=true
user=bekkas
redirect_stderr=true
stdout_logfile=/home/bekkas/web/<domain>/public_html/storage/logs/queue.log
stopwaitsecs=3600
EOF
supervisorctl reread && supervisorctl update
```

The worker auto-restarts on crash (`autorestart=true`). After each deploy, `bin/deploy.sh` automatically restarts the correct program based on `APP_ENV`, so no manual intervention is needed.

Useful Supervisor commands:

```bash
supervisorctl status                     # check all workers
supervisorctl restart <name>-queue       # restart a specific worker
supervisorctl tail -f <name>-queue       # tail its log
```

### PHP CLI — PCNTL requirement

Laravel's queue worker uses `pcntl_signal`. On some server images these functions are disabled in `php.ini`. Check and fix:

```bash
# Verify
php -r "var_dump(function_exists('pcntl_signal'));"
# Expected: bool(true)

# If false, edit /etc/php/8.4/cli/php.ini:
# Back up first
sudo cp /etc/php/8.4/cli/php.ini /etc/php/8.4/cli/php.ini.bak
# Remove only pcntl_* tokens from disable_functions — leave exec, system, etc. alone
# Before: disable_functions = pcntl_alarm,pcntl_fork,pcntl_signal,exec,system
# After:  disable_functions = exec,system
```

> If you also edited the FPM php.ini, restart FPM: `sudo systemctl restart php8.4-fpm`

Security note: enabling PCNTL restores process-control functions. Do not enable other risky functions unless you understand the security implications.

### Local development

In local development the queue driver can stay `sync` so jobs run inline without needing a worker:

```dotenv
QUEUE_CONNECTION=sync
```

`composer dev` also starts a `queue:listen` process if you prefer to test the async path locally.

## 🔧 Development commands

- Start dev server + assets (local):
  - composer dev (concurrently runs the server, queue listener and vite)
- Run migrations:
  - php artisan migrate
- Seed demo data:
  - php artisan db:seed
- Run queue worker:
  - php artisan queue:work
- Run tests (safe helper):
  - chmod +x bin/run-tests.sh         # make the helper executable (once)
  - ./bin/run-tests.sh                # run the full test suite using an isolated sqlite DB (recommended on single servers)
  - FORCE=1 ./bin/run-tests.sh        # bypass safety check (use with care)
  - composer test                     # alternative: runs `php artisan test` (not isolated)
- Lint & format:
  - vendor/bin/pint (Laravel Pint)

---

## 🚚 Deployment

- Branch strategy:
   - `develop` → development
   - `staging` → staging/QA
   - `main` → production
- Full step-by-step deployment playbook:
   - See [DEPLOY.md](DEPLOY.md)

---

## 🔒 Security & Best Practices

- Attachments are stored on a private disk and downloads are validated to belong to the user or admin.
- Key endpoints use throttling to prevent abuse (e.g., adding to cart, favorites, checkout limited).
- Use monitoring (Sentry/Logs) and daily backups for production databases.

---

## 🧾 Notes for maintainers

- Translations: the `t()` helper will fetch DB-driven translations and fall back to Laravel files. After modifying translations, remember to clear cache if necessary.
- Shipping: shipping tiers and regions are configurable in the admin panel and used both in checkout and a public endpoint to retrieve tiers for a postal code.

---

## Contributing

Contributions are welcome. Open a PR with a descriptive title and link to any relevant issue. Consider adding tests for new functionality.

---

## License

MIT
