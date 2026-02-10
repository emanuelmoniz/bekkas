describe('Pay page — full E2E (DB-seed + server-side Easypay stub)', () => {
  it('shows paid message and prevents SDK when latest payment is PAID (server-refreshed)', () => {
    // Seed order + user and return a one-time login URL
    cy.request('POST', '/__cypress/seed-order', {
      payment_id: 'cypress-paid-1',
      payment_status: 'paid'
    }).then((resp) => {
      expect(resp.status).to.equal(200);
      const body = resp.body;
      expect(body).to.have.property('order_id');

      // Ensure server-side Easypay single endpoint would return PAID as well
      cy.request('POST', '/__cypress/mock-easypay', {
        payment_id: body.payment_id,
        response: { id: body.payment_id, payment_status: 'paid', paid_at: new Date().toISOString() }
      }).its('status').should('equal', 200);

      // Visit the one-time login URL to set a valid session cookie
      cy.visit(body.login_url);

      // Finally visit the real pay page for the seeded order
      cy.visit(`/orders/${body.order_id}/pay`);

      // Assert the server-driven paid message is shown
      cy.contains('Payment completed — your order is being processed.').should('be.visible');

      // SDK block must not be present when order is already paid
      cy.get('body').then($b => {
        if ($b.find('#easypay-inline-root').length) {
          cy.get('#easypay-inline-root').should('not.exist');
        } else {
          // container absent — acceptable and expected for a paid order
          expect($b.find('#easypay-inline-root').length).to.equal(0);
        }
    });
  });

  it('shows MB payment information for pending MB payment and does not start the SDK', () => {
    const mbExpiration = new Date(Date.now() + 1000 * 60 * 60 * 24).toISOString();

    cy.request('POST', '/__cypress/seed-order', {
      payment_id: 'cypress-mb-1',
      payment_status: 'pending',
      mb_entity: '111',
      mb_reference: '222',
      mb_expiration: mbExpiration
    }).then((resp) => {
      expect(resp.status).to.equal(200);
      const body = resp.body;

      // Stub the server-side Easypay single-payment response
      cy.request('POST', '/__cypress/mock-easypay', {
        payment_id: body.payment_id,
        response: {
          id: body.payment_id,
          payment_status: 'pending',
          multibanco: { entity: '111', reference: '222', expiration_time: mbExpiration }
        }
      }).its('status').should('equal', 200);

      // ALSO seed an active manifest/session to simulate the race condition
      cy.request('POST', '/__cypress/seed-order', {
        payment_id: body.payment_id,
        payment_status: 'pending',
        manifest: { id: 'm-cypress-1', session: 's1' }
      }).its('status').should('equal', 200);

      cy.visit(body.login_url);
      cy.visit(`/orders/${body.order_id}/pay`);

      // Payment information block should be present with MB values
      cy.contains('Payment information').should('be.visible');
      cy.contains('MB entity').should('be.visible');
      cy.contains('111').should('be.visible');
      cy.contains('MB reference').should('be.visible');
      cy.contains('222').should('be.visible');

      // SDK MUST NOT be present for a persisted pending payment
      cy.get('#easypay-inline-root').should('not.exist');
    });
  });

  it('client SDK onSuccess posts to server and shows returned message (deterministic)', () => {
    cy.request('POST', '/__cypress/seed-order', { payment_id: 'cypress-sdk-1' }).then(resp => {
      const body = resp.body;
      cy.visit(body.login_url);
      cy.visit(`/orders/${body.order_id}/pay`);

      // Intercept server verify endpoint and respond with a friendly message.
      // NON-authoritative: client should show it inline and NOT persist it to session.
      cy.intercept('POST', `/orders/${body.order_id}/pay/verify`, {
        statusCode: 201,
        body: { ok: true, message: 'Payment received — thank you. Updating order status…', payment: { payment_id: 'cypress-sdk-1' }, paymentStatus: 'paid', authoritative: false }
      }).as('verify');

      // New: SDK onClose should redirect to order details (useful when user taps SDK "end" button)
      cy.window().then(win => {
        expect(win.__easypay_onClose).to.be.a('function');
        cy.stub(win.location, 'assign').as('assign');
        // Simulate user pressing SDK "end" button — should redirect to order show
        win.__easypay_onClose();
      });
      cy.get('@assign').should('have.been.calledWith', `/orders/${body.order_id}`);

      // Ensure client does NOT reload and instead shows the message inline
        // wait for the verify request to finish (server round-trip)
        cy.wait('@verify');

        // Client should have recorded the server message (test-only hook) and NOT attempted reload
        cy.window().its('__easypay_lastServerMessage').should('equal', 'Payment received — thank you. Updating order status…');
        cy.get('@reload').should('not.have.been.called');

        // The client inserts the flash into the global shell — assert it appears
        cy.contains('Payment received — thank you. Updating order status…').should('be.visible');

      // After closing the inline flash, the message MUST NOT persist to the order details page
      cy.get('button[aria-label="Close flash message"]').click();
      cy.contains('Payment received — thank you. Updating order status…').should('not.exist');
      cy.visit(`/orders/${body.order_id}`);
      cy.get('div[role="alert"]').should('not.exist');
      // DOM + console-only assertions (no debug helpers): ensure aria-hidden is never true while visible
      cy.get('[data-flash-root]').should('exist').and('be.visible').and(($r) => {
        expect($r.attr('aria-hidden')).to.not.equal('true');
      });

      // The close button must be visible but must NOT receive automatic focus
      cy.get('button[aria-label="Close flash message"]').should('be.visible').should('not.have.focus');

      // Simulate a keyboard user focusing the close button and closing the flash — focus should move out
      cy.get('button[aria-label="Close flash message"]').focus().should('have.focus');
      cy.get('button[aria-label="Close flash message"]').click();
      cy.get('button[aria-label="Close flash message"]').should('not.have.focus');
      cy.contains('Payment received — thank you. Updating order status…').should('not.exist');

      // Final console guard
      const joined = errors.join('\n');
      expect(joined).to.not.contain('aria-hidden');
        });
      });
    });

    it('client SDK onSuccess authoritative-paid redirects to order page and shows server flash', () => {
      cy.request('POST', '/__cypress/seed-order', { payment_id: 'cypress-sdk-2' }).then(resp => {
        const body = resp.body;
        cy.visit(body.login_url);
        cy.visit(`/orders/${body.order_id}/pay`);

        cy.intercept('POST', `/orders/${body.order_id}/pay/verify`, {
          statusCode: 201,
          body: { ok: true, message: 'Payment completed — thank you.', payment: { payment_id: 'cypress-sdk-2' }, paymentStatus: 'paid', authoritative: true }
        }).as('verifyAuth');

        // stub location.href so we can assert the intended navigation without leaving the test
        cy.window().then(win => { cy.stub(win.location, 'assign').as('assign'); expect(win.__easypay_onSuccess).to.be.a('function'); win.__easypay_onSuccess({ id: 'chk_auth', payment: { id: 'cypress-sdk-2', payment_status: 'paid' } }); });
        cy.wait('@verifyAuth');

        // When authoritative+paid the client should navigate to the order show page
        cy.get('@assign').should('have.been.calledWith', `/orders/${body.order_id}`);

        // Simulate server navigation: visit the order and assert the server-persisted flash appears
        cy.visit(`/orders/${body.order_id}`);
        cy.contains('Payment completed — thank you.').should('be.visible');
      });
    });

  it('server-rendered flash supports success/error/warning/info and close-button works (deterministic)', () => {
    const cases = [
      { type: 'success', cls: 'bg-green-100', text: 'Server success' },
      { type: 'error', cls: 'bg-red-100', text: 'Server error' },
      { type: 'warning', cls: 'bg-amber-50', text: 'Server warning' },
      { type: 'info', cls: 'bg-blue-100', text: 'Server info' },
    ];

    cases.forEach(c => {
      const url = `/__cypress/flash?type=${c.type}&message=${encodeURIComponent(c.text)}`;
      cy.request('GET', url).then(() => {
        cy.visit(url);
        cy.contains(c.text).should('be.visible');
        cy.get('div[role="alert"]').should('have.class', c.cls);

        // error should be assertive for a11y
        if (c.type === 'error') {
          cy.get('div[role="alert"]').should('have.attr', 'aria-live', 'assertive');
        }

        cy.get('button[aria-label="Close flash message"]').click();
        cy.contains(c.text).should('not.exist');
      });
    });
  });

  it('does not show a flash on pages without messages (no FOUC)', () => {
    // visit a public page that should not have a server flash and assert no flash is visible
    cy.visit('/');
    cy.get('div[role="alert"]').should('not.be.visible');
    // the close button must not be focusable when the flash is hidden (prevents aria-hidden+focus)
    cy.get('button[aria-label="Close flash message"]').should('have.attr', 'tabindex', '-1');
  });

  it('closing a visible flash moves focus out of the alert (a11y)', () => {
    cy.request('GET', '/__cypress/flash?type=info&message=FocusTest').then(() => {
      cy.visit('/__cypress/flash');

      cy.get('button[aria-label="Close flash message"]').focus().should('be.focused');
      cy.get('button[aria-label="Close flash message"]').click();

      // after close, the focused element must not be inside the alert
      cy.focused().then($el => {
        cy.get('div[role="alert"]').then($alert => {
          expect($alert[0].contains($el[0])).to.equal(false);
        });
      });
    });
  });
});
