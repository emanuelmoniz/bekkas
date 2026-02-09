describe('Easypay pay-page (simulator fixture)', () => {
  beforeEach(() => {
    cy.intercept('POST', '/orders/ORDER-TEST-123/pay/prepare', (req) => {
      req.reply({ action: 'new-manifest', manifest: { id: 'm1' } })
    }).as('prepare')

    cy.intercept('POST', '/easypay/sdk/error', (req) => {
      // emulate server verifying payment and returning already-paid action
      expect(req.body).to.have.property('error')
      req.reply({ action: 'already-paid', message: 'Order already paid' })
    }).as('sdkError')
  })

  it('prepares sdk and handles already-paid SDK error by asking server', () => {
    cy.visit('/test-fixtures/easypay-pay-sim.html')
    cy.get('#prepare').click()
    cy.wait('@prepare')
    cy.get('#start').should('not.be.disabled')
    cy.get('#simulate-already-paid').click()
    cy.wait('@sdkError')
    cy.get('#status').should('contain.text', 'already-paid')
  })
})
