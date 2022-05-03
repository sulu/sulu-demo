import {adminPage, loginPage} from '../../support/pages';

describe('Validate login and logout', () => {
  beforeEach(() => {
    cy.intercept('/admin/logout').as('logout');
    cy.intercept('/admin/login').as('login');
    loginPage.visit();
  });

  it('Login with valid data set cookie and logout remove this cookie', () => {
    loginPage.doLoginAsAdmin();
    cy.wait('@login');
    cy.getCookie('SULUSESSID').should('exist');
    adminPage.openAdminSidebar();
    adminPage.profileHeadline.click();
    adminPage.logout.click();
    cy.wait('@logout');
    cy.getCookie('SULUSESSID').should('not.exist');
  });
});
