import BasePage from './base.page';

export class LoginPage extends BasePage {
  constructor() {
    super();
    this.path = '/admin';
  }

  doLoginAsAdmin() {
    this.inputUsername.clear().type(`${Cypress.env('username')}`);
    this.inputPassword.clear().type(`${Cypress.env('password')}` + '{enter}');
  }

  get inputUsername() {
    return cy.get('input[type=text]');
  }

  get inputPassword() {
    return cy.get('input[type=password]');
  }
}

export const loginPage = new LoginPage();
