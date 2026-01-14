Testing the BEKKAS app
======================

This document explains how to run tests safely on your single Ubuntu server and how GitHub Actions (CI) does automated testing.

1) Goals and safety
- Tests must not touch or corrupt production data.
- Tests should run in isolation and be repeatable.
- If your host is also the production host, use an isolated sqlite DB (this repo ships a small helper script).

2) Quick safest way to run tests on your Ubuntu server
- Install dependencies (if not already):
  - PHP & extensions: sudo apt update && sudo apt install php php-cli php-sqlite3 php-xml php-mbstring git unzip -y
  - Composer: follow https://getcomposer.org/download/
  - Node & npm: sudo apt install nodejs npm (or use NodeSource for a specific Node version)

- Install project dependencies in the repo (run in `public_html`):
  - composer install --no-interaction --prefer-dist
  - npm ci

- Make the helper script executable (needed once):
  - chmod +x bin/run-tests.sh

- Run tests using the helper script (isolated sqlite DB in `database/testing.sqlite`):
  - ./bin/run-tests.sh

Notes:
- The script sets APP_ENV=testing and DB_CONNECTION=sqlite and migrates only the temporary sqlite DB so your production DB and data remain untouched.
- If your PHP build is missing SQLite support (pdo_sqlite), install `php-sqlite` and retry.
- Tests disable reCAPTCHA validation during test runs even if RECAPTCHA keys are present on the server (the test suite sets `services.recaptcha.secret_key` to null to avoid external requests).
- CSRF verification is disabled for tests (the test bootstrap calls `withoutMiddleware()` to disable middleware) so form POSTs from tests don't fail with 419.
- If you want to inspect test logs, you can redirect output to a file: ./bin/run-tests.sh | tee /tmp/tests.log

3) How CI (GitHub Actions) runs tests (overview)
- When you push to a branch or open a pull request, the configured workflow (.github/workflows/ci.yml) performs these steps:
  1. Checkout repository
  2. Setup PHP and Node versions
  3. Install Composer and npm dependencies
  4. Create env (copy .env.example, generate app key)
  5. Prepare a database (usually SQLite in CI or a service like MySQL via Actions services)
  6. Run formatting checks (Pint), tests (phpunit), and build assets (npm run build)
- CIs run in ephemeral runners (containers/VMs) and don't affect your production server.

4) Recommendations for your setup
- Prefer running tests in CI (GitHub Actions) to avoid relying on your single production server. If you don't have another host, use the helper script above and run it during off-hours.
- Add a dedicated test user on the server and run tests under that user to minimize side effects.
- Configure your `mail` to `array`/`log` during tests to avoid sending real emails; the helper script sets `MAIL_MAILER=array` for safety.
- Mock external services (payment gateways, third-party APIs) in tests or provide test credentials and ensure tests do not perform destructive operations.

5) Integrating automated tests with GitHub (short how-to)
- Commit and push your `.github/workflows/ci.yml` file (already added). GitHub Actions will pick it up automatically for pushes and pull requests against configured branches (main/master).
- For private repos: configure GitHub secrets if CI needs API keys. Put them in repository Settings → Secrets.
- Review workflow runs in the repository's Actions tab. Fix failures and iterate until the workflow succeeds on PRs.

6) When to disable tests from README
- Until you're confident the test suite is stable and documented, you asked to remove the Tests section from the main README. The tests are still present and runnable (see TESTING.md), but the top-level README won't prompt people to run them until you're ready.

If you'd like, I can also:
- Add a cron or systemd timer to run tests nightly and email/report results, or
- Add a Git hook or a simple script you can trigger via SSH to run tests and upload results to a report location.

Creating an admin user
- To create an administrator user (Administrator / emanuel.moniz@bekkas.pt / Abc.123) run:
  - php artisan db:seed --class=AdminUserSeeder
- The seeder will create the `admin` role if it doesn't exist and attach it to the user.
- **Security note:** change the password immediately after login and remove or restrict the seeder if you keep it in the repo.

Feedback welcome — tell me whether you want a nightly test runner or a simple SSH-triggered script next.