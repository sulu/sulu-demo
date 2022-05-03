import BasePage from '../../support/pages/base.page';

describe('Count the number of artist on the artist page', () => {
    beforeEach(function ()  {
        cy.fixture('artist-count.json').then((ArtistCounter) => {
            this.ArtistCounter = ArtistCounter;
        });
    });

    context('EN: artist page', () => {

        it('Load page', () => {
            cy.visit(`${Cypress.env('URL')}/en/artists`);
        });

        it('Count and compare with fixture data', function(){
            cy.wrap(this.ArtistCounter.artistsCount.en).should('gt',0);
            cy.get('.card').should('have.length', this.ArtistCounter.artistsCount.en);
            cy.log('Succesfully counted and compared', this.ArtistCounter.artistsCount.en);
        });
    });

    context('DE: Musiker Seite', () => {
        it('Load page', () => {
            cy.visit(`${Cypress.env('URL')}/de/musiker`);
        });

        it('Count and compare with fixture data', function(){
            cy.wrap(this.ArtistCounter.artistsCount.de).should('gt',0);
            cy.get('.card').should('have.length', this.ArtistCounter.artistsCount.de);
            cy.log('Succesfully counted and compared', this.ArtistCounter.artistsCount.de);
        });
    });

});