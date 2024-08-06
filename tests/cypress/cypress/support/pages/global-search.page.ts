import BasePage from './base.page';

export class GlobalSearchPage extends BasePage {
  get searchDropDown() {
    return cy.get('main button');
  }

  get searchInputField() {
    return cy.get('main input');
  }

  get searchIcon() {
    return cy.get('main span[aria-label="su-search"]');
  }

  get searchInputReset() {
    return cy.get('main span[aria-label="su-times"]');
  }

  clickCategoryDropdown(category: string) {
    this.searchDropDown.click();
    cy.get('button[class^=item--]').each(item => {
      if (item.text() === category) {
        item.trigger('click');
      }
    });
  }
}

export const globalSearchPage = new GlobalSearchPage();
