# Deploy Guide

## Runtime prerequisites

- Node.js: `20.19.0` (or `>=22.12.0`)
- npm: use the version bundled with your Node install

Use the project-pinned Node version before install/build steps:

```bash
nvm install
nvm use
node -v
npm -v
```

This project uses a 3-phase branch promotion flow:

- `develop` → Development (`dev.bekkas.pt`)
- `staging` → Staging / QA (`tes.bekkas.pt`)
- `main` → Production (`bekkas.pt`)

## One-time branch setup (if missing)

If `staging` and `main` do not exist yet:

```bash
git checkout develop
git pull origin develop

git checkout -b staging
git push -u origin staging

git checkout -b main
git push -u origin main

git checkout develop
```

## Phase 1 — Feature work (develop)

1. Start from updated `develop`

```bash
git checkout develop
git pull origin develop
```

2. Create feature branch

```bash
git checkout -b feature/my-change
```

3. Implement changes, then run checks

```bash
./bin/run-tests.sh
./vendor/bin/pint
```

4. Commit and push

```bash
git add .
git commit -m "feat: describe change"
git push origin feature/my-change
```

5. Open PR and merge into `develop`
   - `develop` auto-deploys to `dev.bekkas.pt`.

## Phase 2 — Promote to staging for QA

From your local repo:

```bash
git checkout develop
git pull origin develop

./bin/run-tests.sh

git checkout staging
git pull origin staging
git merge develop
git push origin staging
```

On the staging server:

```bash
cd ~/web/<staging-domain>/public_html
./bin/deploy.sh
```

## Phase 3 — Promote to production

From your local repo:

```bash
git checkout staging
git pull origin staging

git checkout main
git pull origin main
git merge staging
git push origin main
```

On the production server:

```bash
cd ~/web/<prod-domain>/public_html
./bin/deploy.sh --skip-seed
```

## Quick reference

```bash
# Feature flow
git checkout develop && git pull origin develop
git checkout -b feature/name
# ...work...
./bin/run-tests.sh && ./vendor/bin/pint
git add . && git commit -m "feat: ..."
git push origin feature/name

# Promote develop -> staging
git checkout develop && git pull origin develop
./bin/run-tests.sh
git checkout staging && git pull origin staging
git merge develop && git push origin staging
# on staging server
./bin/deploy.sh

# Promote staging -> main
git checkout staging && git pull origin staging
git checkout main && git pull origin main
git merge staging && git push origin main
# on production server
./bin/deploy.sh --skip-seed
```

## Notes

- Run deploy commands from project root (`public_html`).
- `./bin/deploy.sh` handles dependency install, build, migrations, storage link, cache clear, and cache rebuild (for staging/production).
- If deployment ever fails with a stale package/provider cache issue, rerun deploy script after ensuring repo is clean; script already clears stale bootstrap package/service cache files before Composer install.