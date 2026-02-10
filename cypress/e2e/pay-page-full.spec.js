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
});
