// Updated spec with desktop/mobile behavior

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
          .should('include', '"autoplay_desktop":false')
          .should('include', '"autoplay_mobile":false');

        // verify hovering parent anchor (and descendants) kicks off movement
        const parentLink = $el.closest('a');
        if (parentLink) {
          const initialTransform = $el.css('transform');
          cy.wrap(parentLink).trigger('pointerover');
          cy.wait(3200);
          cy.wrap($el).should($sc => {
            const newTransform = $sc.css('transform');
            expect(newTransform).not.to.equal(initialTransform);
          });

          cy.wrap(parentLink).trigger('pointerout');
          cy.wait(500);

          // also try hovering the first slide directly (photo zone) to mimic
          // the problematic case; events should bubble to the link target.
          const firstSlide = $el.find('.slide').first();
          if (firstSlide.length) {
            const before = $el.css('transform');
            cy.wrap(firstSlide).trigger('pointerover');
            cy.wait(3200);
            cy.wrap($el).should($sc => {
              expect($sc.css('transform')).not.to.equal(before);
            });
            cy.wrap(firstSlide).trigger('pointerout');
          }
        }
      });
    });
  });

  it('in mobile viewport the scroller autoplays without interaction', () => {
    cy.viewport('iphone-6');
    cy.visit('/store');

    cy.get('[data-image-scroller]').first().then($el => {
      if ($el.length === 0) return;
      const initial = $el.css('transform');
      cy.wait(3200);
      cy.wrap($el).should($sc => {
        expect($sc.css('transform')).not.to.equal(initial);
      });
    });
  });
});
