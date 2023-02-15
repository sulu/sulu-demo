export default class NavigationToggler {
    initialize(el, options) {
        this.toggler = el;
        this.navigation = document.querySelector(options.navigation);
        this.togglerActiveIconClass = options.togglerActiveIconClass || 'icon--times';
        this.togglerInactiveIconClass = options.togglerInactiveIconClass || 'icon--menu';

        this.toggler.addEventListener('click', this.click.bind(this));
    }

    click() {
        this.navigation.style.display = this.navigation.style.display === 'flex' ? 'none' : 'flex';
        this.toggler.classList.toggle(this.togglerActiveIconClass);
        this.toggler.classList.toggle(this.togglerInactiveIconClass);
    }
}
