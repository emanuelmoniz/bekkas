describe('Pay page — payment-status driven UI', () => {
  it('shows paid message and hides SDK when latest payment is PAID', () => {
    cy.visit('/test-fixtures/pay-paid.html');
    cy.contains('Payment is done').should('be.visible');
    cy.get('#easypay-sdk').should('not.exist');
  });

  it('shows MB payment information for pending MB payment', () => {
    cy.visit('/test-fixtures/pay-mb.html');
    cy.contains('Payment is pending').should('be.visible');
    cy.get('#payment-information').within(() => {
      cy.get('#mb-entity').should('contain.text', '111');
      cy.get('#mb-reference').should('contain.text', '222');
      cy.get('#mb-expiration').should('contain.text', '2026-03-01');
    });
    cy.get('#easypay-sdk').should('not.exist');
  });
});
