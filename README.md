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

## CLI PHP & queue workers (server notes)

If you run Laravel queue workers on this server you must ensure the PHP CLI has the PCNTL functions available (Laravel's worker uses `pcntl_signal`). On some control-panel images these functions are disabled in `disable_functions`.

- Backup the CLI php.ini before editing:

```bash
sudo cp /etc/php/8.4/cli/php.ini /etc/php/8.4/cli/php.ini.bak
```

- In the php.ini entry `disable_functions` remove any tokens that start with `pcntl_` (leave other disabled functions such as `exec` or `system` alone). Example change:

Before:

```text
disable_functions = pcntl_alarm,pcntl_fork,pcntl_signal,exec,system
```

After:

```text
disable_functions = exec,system
```

- Verify PCNTL is available for the CLI:

```bash
php -r "var_dump(function_exists('pcntl_signal'));"
```

Expect `bool(true)`; then run the worker as normal:

```bash
php artisan queue:work
```

- If you edited the FPM php.ini (`/etc/php/8.4/fpm/php.ini`) restart FPM:

```bash
sudo systemctl restart php8.4-fpm
```

- Quick testing alternative (no ini edit): set the queue driver to `sync` in your `.env` so jobs run inline while you test:

```bash
sed -i 's/^QUEUE_CONNECTION=.*/QUEUE_CONNECTION=sync/' .env
php artisan config:clear
```

Security note: enabling PCNTL restores process-control functions. Do not enable other risky functions unless you understand the security implications.

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
