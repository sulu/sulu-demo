import {startpageClass} from '../../support/pages';

describe('Validate main navigation features', () => {
  beforeEach(() => {
    startpageClass.visit();
  });

  it('Every page has status code 200 and no redirect', () => {
    startpageClass.navigationMainItems.each(item => {
      const url = item.attr('href');

      cy.request({
        url: url,
        followRedirect: false,
      }).then(resp => {
        expect(resp.status).to.eq(200);
      });
    });
  });
});
