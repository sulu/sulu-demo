import {adminPage, globalSearchPage, loginPage} from '../../support/pages';

describe('Use global search', () => {
  beforeEach(() => {
    loginPage.visit();
    cy.intercept('/admin/login').as('login');
    cy.intercept('/admin/search/query?q=*').as('search');
    loginPage.doLoginAsAdmin();
    cy.wait('@login');

    globalSearchPage.openAdminSidebar();
    adminPage.menuSearchButton.click();
  });

  it('show search page', () => {
    adminPage.menuSearchButton.click();
    globalSearchPage.searchDropDown.should('be.visible').and('contain', 'Everything');
    globalSearchPage.searchInputField.should('be.visible');
    globalSearchPage.searchIcon.should('be.visible');
  });

  it('type "Blog" in input and reset will clear input', () => {
    globalSearchPage.searchInputField.type('Blog').and('have.value', 'Blog');
    globalSearchPage.searchInputReset.first().click();
    globalSearchPage.searchInputField.should('have.value', '');
  });

  it('search in everything for Blog per {enter}', () => {
    globalSearchPage.searchInputField.type('Blog{enter}');
    cy.wait('@search');
    cy.get('div[class^=search-result--]').should('have.length.gt', 0);
  });

  it('search in everything for Blog per button', () => {
    globalSearchPage.searchInputField.type('Blog');
    globalSearchPage.searchIcon.click();
    cy.wait('@search');
    cy.get('div[class^=search-result--]').should('have.length.gt', 0);
  });

  it('click dropdown select Snippets for Demo', () => {
    globalSearchPage.clickCategoryDropdown('Snippets');
    globalSearchPage.searchInputField.type('Demo{enter}');
    cy.wait('@search');
    cy.get('div[class^=search-result--]').should('have.length.gt', 0);
  });

  it('click dropdown select Media for Demo', () => {
    globalSearchPage.clickCategoryDropdown('Media');
    globalSearchPage.searchInputField.type('Demo{enter}');
    cy.wait('@search');
    cy.get('div[class^=search-result--]').should('have.length', 0);
  });

  it('search again in other category for Demo', () => {
    globalSearchPage.clickCategoryDropdown('Snippets');
    globalSearchPage.searchInputField.type('Demo{enter}');
    cy.wait('@search');
    cy.get('div[class^=search-result--]').should('have.length.gt', 0);

    globalSearchPage.clickCategoryDropdown('Media');
    cy.wait('@search');
    cy.get('div[class^=search-result--]').should('have.length', 0);
  });
});
