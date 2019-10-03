import $ from 'jquery';

export default class SlideIn {
    initialize($el, options) {
        this.$el = $el;
        this.delay = options.delay || 0;
        this.direction = options.direction || 'left';
        this.offset = options.offset || 200;

        this.revealClass = 'slide-in--' + this.direction + ' slide-in';
        this.visibleClass = this.revealClass + '--visible';

        this.$el.addClass(this.revealClass);

        this.boundScroll = this.scroll.bind(this);
        window.addEventListener('scroll', this.boundScroll, {
            passive: true
        });
        this.scroll();
    }

    scroll() {
        const scrollTop = $(window).scrollTop();
        const height = $(window).height();
        const visibleTop = scrollTop + height;
        const top = this.$el.offset().top;

        if (top + this.offset <= visibleTop) {
            setTimeout(function() {
                this.$el.addClass(this.visibleClass);
                window.removeEventListener('scroll', this.boundScroll, {
                    passive: true
                });
            }.bind(this), this.delay);
        }
    }
}
