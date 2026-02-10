E2E tests (Cypress)

Quickstart (dev):

1. Start backend + assets (dev):
   - composer dev
   - or: php artisan serve & npm run dev

2. Install Cypress (once):
   - npm ci
   - npx cypress verify

3. Run the Easypay E2E locally:
   - npm run cypress:open  (interactive)
   - npm run cypress:run   (headless)

Scope of the included test:
- (Removed) The easypay SDK client-callback E2E spec and its fixture (`easypay-pay-sim.html`) were removed — callbacks are no longer exercised by the client.
- The fixture simulates the pay page and the SDK callbacks; the test stubs the network endpoints and asserts the client + server interactions.

Notes:
- This E2E is intentionally lightweight and deterministic (network stubbing) so it can run in CI without a real Easypay account.
- Add a full integration E2E if you want to exercise the live SDK in a staging environment.