describe('Image scroller behavior', () => {
  it('store page scrollers are marked autoplay:false and respond to hover on card', () => {
    cy.visit('/store');

    cy.get('[data-image-scroller]').then($els => {
      if ($els.length === 0) {
        // no products/scrollers - nothing to assert
        return;
      }

      cy.wrap($els).each($el => {
        cy.wrap($el)
          .invoke('attr', 'data-image-scroller')
          .should('include', '"autoplay":false');

        // verify hovering parent anchor kicks off movement
        const parentLink = $el.closest('a');
        if (parentLink) {
          const initialTransform = $el.css('transform');
          cy.wrap(parentLink).trigger('pointerenter');
          // wait slightly more than interval (3s configured by view)
          cy.wait(3200);
          cy.wrap($el).should($sc => {
            const newTransform = $sc.css('transform');
            expect(newTransform).not.to.equal(initialTransform);
          });
        }
      });
    });
  });
});