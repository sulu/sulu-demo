export default class BasePage {
  path = '/';

  get url() {
    return `${Cypress.env('URL')}${this.path}`;
  }

  get navigationMainItems() {
    return cy.get('#navigation .navbar__item');
  }

  visit(link = '') {
    if (link != '') {
      link = `${Cypress.env('URL')}${link}`;
    }
    cy.visit(link || this.url);
    return this;
  }

  waitUntilRedirectedTo() {
    cy.log('wait until redirect');
    cy.waitUntil(
      () => {
        return cy.url().then($url => {
          const actualPath = new URL($url).pathname;
          cy.log('Redirect:' + actualPath + ' - ' + this.path);
          return new RegExp(`^${this.path}$`).test(actualPath);
        });
      },
      {interval: 1000}
    );
  }

  openAdminSidebar() {
    cy.log('Open admin sidebar');

    cy.get('header').then($body => {
      const text = $body[0].innerHTML;

      // bars is not open
      if (text.includes('su-bars')) {
        cy.get('span[aria-label=su-bars]').then(el => {
          el.trigger('click');
          cy.get('span[aria-label=su-stick-right]').then(stick => {
            cy.log('Click arrow right');
            stick.trigger('click');
          });
        });
      }
    });
  }

  get actualDateString() {
    const today = new Date();
    const day = String(today.getDate()).padStart(2, '0');

    const month = String(today.getMonth() + 1).padStart(2, '0');
    const year = today.getFullYear();
    const hour = today.getHours();
    const minute = (today.getMinutes() < 10 ? '0' : '') + today.getMinutes();

    return year + '-' + month + '-' + day + '_' + hour + minute;
  }
}
