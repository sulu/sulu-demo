import $ from 'jquery';

export default class CountryFilter {
    initialize($el) {
        this.$items = $('#' + $el.attr('id') + '-container');
        this.selectionElement = $el.change(this.filterCountries.bind(this));
    }

    filterCountries() {
        const countryId = $(this.selectionElement).children(':selected').attr('id');

        if (countryId === 'all') {
            this.$items.children('.none').removeClass('none');
        } else {
            this.$items.children('.none').removeClass('none');
            this.$items.children(':not(.' + countryId + ')').addClass('none');
        }
    }
}
