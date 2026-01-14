# BEKKAS - 3D Printing Studio (Laravel)

A modern e-commerce and support site for a 3D printing studio built with Laravel 12, Vite, Tailwind CSS and Alpine.js.

---

## 🚀 Quickstart

**Requirements:**
- PHP 8.2+
- Composer
- Node 18+ / npm
- MySQL (or a supported DB)

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
- Laravel 12 (PHP ^8.2)
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

## 🧪 Tests & CI

There are currently no automated tests or CI workflows in the repository. Recommended next steps:
- Add PHPUnit / Laravel feature tests for checkout and order placement.
- Add a GitHub Actions workflow to run `composer test` and `npm run build` on PRs.

---

## 🔧 Development commands

- Start dev server + assets (local):
  - composer dev (concurrently runs the server, queue listener and vite)
- Run migrations:
  - php artisan migrate
- Seed demo data:
  - php artisan db:seed
- Run queue worker:
  - php artisan queue:work
- Lint & format:
  - vendor/bin/pint (Laravel Pint)

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
