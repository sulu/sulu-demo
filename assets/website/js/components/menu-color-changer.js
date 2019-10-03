import $ from 'jquery';

export default class MenuColorChanger {
    initialize($el) {
        this.header = $el;
        this.menuContainer = $('#menu-container');
        this.changeColor();
    }

    changeColor() {
        const classList = this.header.prop('classList');
        let color = 'default';
        let i;
        for (i = 0; i < classList.length; i++) {
            if (classList[i].includes('--white')) {
                color = 'white';
                break;
            } else if (classList[i].includes('--black')) {
                color = 'black';
                break;
            }
        }

        this.menuContainer.addClass('menu--' + color);
        this.menuContainer.find('.menu__logo').addClass('menu__logo--' + color);
        this.menuContainer.find('.navigation__link').removeClass('navigation__link--active');
        this.menuContainer.find('.navigation__link').addClass('navigation__link--active--' + color);
        this.menuContainer.find('.navigation__link').addClass('navigation__link--' + color);
    }
}
