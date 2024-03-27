import BasePage from './base.page';

export class AdminPage extends BasePage {
  constructor() {
    super();
    this.path = '/admin/';
  }

  get profileHeadline() {
    return cy.get('body').contains('Adam Ministrator');
  }

  get logout() {
    return cy.get('body').contains('Log out');
  }

  get menuSearchButton() {
    return cy.contains('Search');
  }
}

export const adminPage = new AdminPage();
