describe('Home banner carousel', () => {
  beforeEach(() => {
    cy.visit('/');
  });

  it('renders extra cloned slides on each side when there are >2 slides', () => {
    // the welcome page defines 3 distinct slides; after cloning we expect 3 + 2*2 = 7 elements
    cy.get('section[x-data]')
      .find('div.flex-none')
      .should('have.length', 7);

    // the first two elements should be clones of the final two real slides
    cy.get('section[x-data] div.flex-none').eq(0)
      .should('have.css', 'background-image')
      .and('match', /slide2\.jpg/);
    cy.get('section[x-data] div.flex-none').eq(1)
      .should('have.css', 'background-image')
      .and('match', /slide3\.jpg/);

    // the last two elements should be clones of the first two real slides
    cy.get('section[x-data] div.flex-none').eq(5)
      .should('have.css', 'background-image')
      .and('match', /slide1\.jpg/);
    cy.get('section[x-data] div.flex-none').eq(6)
      .should('have.css', 'background-image')
      .and('match', /slide2\.jpg/);
  });


});