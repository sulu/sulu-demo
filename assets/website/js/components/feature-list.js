import $ from 'jquery';

export default class FeatureList {
    initialize($el) {
        this.section = $el.parent().parent().attr('class');
        if (this.section.includes('--even')) {
            this.activeClass = 'feature__item--active-even';
        } else {
            this.activeClass = 'feature__item--active-odd';
        }

        this.$items = $el.children('div');
        this.$items.click(this.setActive.bind(this));
    }

    setActive(event) {
        this.$items.removeClass(this.activeClass);
        $(event.currentTarget).addClass(this.activeClass);
    }
}
