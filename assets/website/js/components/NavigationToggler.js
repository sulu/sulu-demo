import $ from 'jquery';

export default class NavigationToggler {
    initialize($el, options) {
        this.toggler = $el;
        this.navigation = options.navigation;
        this.togglerActiveIconClass = options.togglerActiveIconClass || 'icon icon--times';
        this.togglerInactiveIconClass = options.togglerInactiveIconClass || 'icon icon--menu';

        // Needed to let jquery know to set display to flex when calling fadeIn
        $(this.navigation).css('display', 'flex').hide();

        $(this.toggler).click(this.click.bind(this));
    }

    click() {
        $(this.navigation).fadeToggle('fast');
        $(this.toggler).toggleClass(this.togglerActiveIconClass);
        $(this.toggler).toggleClass(this.togglerInactiveIconClass);
    }
}
