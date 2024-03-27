import {startpageClass} from '../../support/pages';

describe('Check SEO best practice', () => {
  beforeEach(() => {
    startpageClass.visit();
  });

  it('Only is one H1 on the page', () => {
    cy.get('h1').should('have.length', 1);
  });
});
