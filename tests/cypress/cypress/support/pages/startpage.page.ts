import BasePage from './base.page';

export class StartpageClass extends BasePage {
  constructor() {
    super();
    this.path = '/de';
  }
}

export const startpageClass = new StartpageClass();
