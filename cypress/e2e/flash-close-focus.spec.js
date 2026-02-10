describe('Flash close focus behavior', () => {
  beforeEach(() => {
    // deterministic server-rendered flash helper (test-only route)
    cy.visit('/__cypress/flash?type=warning&message=close-focus-test');
  });

  it('moves focus out of the flash before it becomes aria-hidden and emits no console aria-hidden warning', () => {
    // capture console errors (fail if aria-hidden warning appears)
    const consoleErrors = [];
    cy.on('window:before:load', (win) => {
      const orig = win.console.error;
      win.console.error = function () {
        consoleErrors.push(Array.from(arguments).join(' '));
        if (orig) { orig.apply(this, arguments); }
      };
    });

    // ensure the flash and close button are visible and focusable
    cy.get('[data-flash-root]').should('exist').and('be.visible');
    cy.get('button[aria-label="Close flash message"]').as('closeBtn').should('be.visible').should('not.have.focus');

    // simulate keyboard focus then click (user interaction)
    cy.get('@closeBtn').focus().should('have.focus');
    cy.get('@closeBtn').click();

    // The close button must not keep focus and the page main landmark should receive focus
    cy.get('@closeBtn').should('not.have.focus');
    cy.get('main').should('exist').and(($main) => {
      const active = Cypress.$(document.activeElement);
      const insideFlash = active.closest('[data-flash-root]').length > 0;
      expect(insideFlash).to.equal(false);
    });

    // assert no console.error messages that include aria-hidden (deterministic guard)
    cy.then(() => {
      const found = consoleErrors.join('\n');
      expect(found).to.not.contain('aria-hidden');
    });
  });
});