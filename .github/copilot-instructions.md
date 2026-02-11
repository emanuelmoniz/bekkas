# GitHub Copilot instructions for BEKKAS (Laravel)

## Quick summary ✅
- BEKKAS is a Laravel 12 e‑commerce + support app (PHP 8.2): product catalog, cart/checkout, shipping tiers, orders, tickets.
- **Key conventions:** DB-driven static translations via `t()` (cached), thin controllers delegating to `app/Services`, and `*Translation` models for DB strings.

## First things an AI should know (big picture) 💡
- Architecture: Laravel MVC. Business rules live in `app/Services` (e.g., `ShippingCalculator`, `DeliveryDateCalculator`, `DefaultShippingTierResolver`). Controllers should remain thin and use `app/Http/Requests` for validation.
- i18n: primary source is DB (static translations). Use the `t()` helper and update `database/seeders/StaticTranslationsSeeder.php` when adding UI strings.
- Shipping & delivery: configurable via `app/Models/ShippingConfig.php` and `app/Models/ShippingTier.php` — modify service code and update seeders/tests together.
- Admin surface: routes under `admin` prefix + `is_admin` middleware. Sensitive endpoints use throttling (see `routes/web.php`).

## How to run, build & test (exact commands) 🔧
- Setup: `composer setup` (installs deps, copies `.env`, generates key, optionally runs migrations, builds assets).
- Dev (recommended): `composer dev` (runs server, `queue:listen`, `php artisan pail --timeout=0`, and `npm run dev` concurrently via npx concurrently).
- Frontend: `npm run dev` (dev) / `npm run build` (production).
- Storage: `php artisan storage:link` (expose `public` disk locally).
- Tests (safe helper):
  - `chmod +x bin/run-tests.sh` (once)
  - `./bin/run-tests.sh` (runs tests using an isolated SQLite DB at `database/testing.sqlite`)
  - `./bin/run-tests.sh --filter Name` (pass filter / PHPUnit args)
  - `FORCE=1 ./bin/run-tests.sh` (bypass safety checks)
  - The helper exports: `APP_ENV=testing`, `DB_CONNECTION=sqlite`, `DB_DATABASE=database/testing.sqlite`, `MAIL_MAILER=array`, `CACHE_DRIVER=array`, `SESSION_DRIVER=array` and runs migrations explicitly against that DB.
- Lint/format: `./vendor/bin/pint` (CI enforces it).
- My server is live and the development is done there, its production env. Run migrations and clear the views, cache, routes, configs, etc. after changes when needed:
  - `php artisan migrate`
  - `php artisan view:clear`
  - `php artisan cache:clear`
  - `php artisan route:clear`
  - `php artisan config:clear`

## Testing & patterns to mimic in new tests (concrete examples) 📋
- Use `RefreshDatabase` and model factories (`tests/Feature/*`, `tests/Unit/*`).
- Simulate a cart: `withSession(['cart' => [$product->id => <qty>]])` in feature tests.
- Control time with `Carbon::setTestNow()` for deterministic assertions.
- Prefer asserting services directly in unit tests (e.g., `ShippingCalculator::calculate`) and use feature tests for end-to-end flows (checkout → order creation).
- Note: `tests/TestCase.php` disables CSRF (`VerifyCsrfToken`) and rate-limiting (`ThrottleRequests`) middleware to avoid flaky 419/429 failures, shares an empty `$errors` ViewErrorBag, and sets a predictable `X-CSRF-TOKEN` header for requests.

## Project-specific conventions (do this, not generic advice) ⚠️
- **Admin pages — English only.** Admin UI strings must remain English and **must not** be added to DB translations or referenced via `t()`. Any exception requires an explicit review and justification in the PR.
- **Client pages — multilingual.** All client-facing static text must be added to `database/seeders/StaticTranslationsSeeder.php` and referenced with `t()`; include entries for every supported locale and clear the translation cache (`php artisan cache:forget static_translations_all` or `php artisan cache:clear`) after seeding.
- Always use `t('...')` for user-facing strings that appear in UI. When adding/updating keys: update `database/seeders/StaticTranslationsSeeder.php`, run or re-seed, and clear the translation cache (the helper caches under `static_translations_all` — `php artisan cache:forget static_translations_all` or `php artisan cache:clear`).
- Translation models follow `XTranslation` with no timestamps (see `ProductTranslation.php`).
- Preserve backward compatibility for denormalized columns (example: `Product::getTaxAttribute` may return a model-like object).
- Business logic must live in `app/Services` (not in controllers). If you add logic to a controller, also add a service and unit tests.
- Respect existing throttles and middleware when touching public endpoints (e.g. `cart.add`, `checkout.place`).

## Integration points & external concerns to check for PRs 🔎
- Mail/notifications: `app/Mail` and `app/Notifications` — tests run with `MAIL_MAILER=array` and CI sets safe defaults.
- File storage: `public` and `private` disks; use `php artisan storage:link` in dev.
- Third-party/infra: check `.env` keys in `README.md` (DB, MAIL, RECAPTCHA, AWS_* if S3 is used). Tests nullify reCAPTCHA secrets; prefer fakes for external APIs.

## Files & places to change (concrete checklist) 🗂️
- `routes/web.php` (public vs auth vs admin; note throttle usage)
- `app/Http/Requests/*` (validation messages must use `t()`)
- `app/Services/*` (business logic — add unit tests here)
- `app/helpers.php` and `app/Models/*Translation.php` (DB-driven translations)
- `database/seeders/StaticTranslationsSeeder.php`, `AdminUserSeeder`, and `ShippingTier` seeders
- `bin/run-tests.sh` and `TESTING.md` (test helper and recommendations)
- `tests/Feature/*` (flows) and `tests/Unit/*` (services)

## Common pitfalls for PR reviewers (what to watch for) ⚠️
- Forgetting to add DB translation keys and seed updates when changing UI text.
- Breaking backward-compat denormalized columns (see `Product::getTaxAttribute`).
- Changing checkout/shipping logic without adding tests that assert: stock decrement, shipping-tier fallback, expected delivery date (examples in `tests/Feature/CheckoutTest.php`, `ShippingTest.php`).
- Introducing time-sensitive behavior without deterministic tests (use `Carbon::setTestNow`).

## Example prompts you can give an AI agent (copy/paste) ✍️
- "Add a unit test for `ShippingCalculator::calculate` covering weight > defined tiers and update `ShippingTier` seeder." 
- "Implement VAT field validation for checkout: update `StoreOrderRequest`, add `t()` keys in `StaticTranslationsSeeder`, and a feature test in `tests/Feature/CheckoutTest.php`."
- "Refactor `OrderController::place` to move pricing logic into `app/Services/OrderBuilder.php` and add unit tests." 

## Non-goals / Do NOT change without human sign-off 🚫
- Do not remove DB-driven `t()` translations or replace them with file-based strings without providing seed + migration + tests.
- Do not modify throttling or admin middleware semantics without updating routes/tests and security rationale in the PR description.

---
If anything above is unclear or you'd like added examples (e.g. a sample unit test or a checklist for shipping-related PRs), tell me which area to expand and I'll update this file. 👍

---

## Condensed / updated guidance for AI agents ✨
- Keep it short: prioritise commands, files to check, and concrete examples.
- Key files: `app/Services/*`, `app/helpers.php` (`t()`), `database/seeders/StaticTranslationsSeeder.php`, `app/Models/ShippingConfig.php`, `app/Models/ShippingTier.php`, `routes/web.php`, `bin/run-tests.sh`, `TESTING.md`, `AdminUserSeeder`.
- Commands (exact): `composer setup`, `composer dev`, `npm run dev`, `npm run build`, `chmod +x bin/run-tests.sh`, `./bin/run-tests.sh`, `FORCE=1 ./bin/run-tests.sh`, `composer test`, `./vendor/bin/pint`.
- Tests: helper creates `database/testing.sqlite`, sets `APP_ENV=testing`, `DB_CONNECTION=sqlite`, `MAIL_MAILER=array`, disables CSRF & throttles—use it for safe local runs.
- Conventions: always use `t('...')`, put business rules in `app/Services`, update seeders/tests together when changing checkout/shipping/tax logic.~
- Allways use a deterministica approach to debug and find errors.
- This is the server that i am runing. Allways run cmds to clear cache, views, routes, configs, etc. after changes when needed:
  - `php artisan view:clear`
  - `php artisan cache:clear`
  - `php artisan route:clear`
  - `php artisan config:clear`
  and run migrations when needed:
  - `php artisan migrate`
  and npm run build when needed:
  - `npm run build`

---