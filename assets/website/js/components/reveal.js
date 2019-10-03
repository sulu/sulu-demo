import $ from 'jquery';

export default class Reveal {
    initialize($el, options) {
        this.$el = $el;
        this.$children = this.$el.children();
        this.delay = options.delay || 150;

        this.revealClass = 'reveal';
        this.visibleClass = this.revealClass + '--visible';

        this.$children.addClass(this.revealClass);

        $(window).scroll(this.scroll.bind(this));
        this.scroll();
    }

    scroll() {
        const scrollTop = $(window).scrollTop();
        const height = $(window).height();
        const visibleTop = scrollTop + height;
        const that = this;

        let i = 1;
        this.$children.each(function() {
            const $child = $(this);

            if ($child.hasClass(that.visibleClass)) {
                return;
            }

            const top = $child.offset().top;
            if (top <= visibleTop) {
                if (top + $child.height() > scrollTop) {
                    setTimeout(function() {
                        $child.addClass(that.visibleClass);
                    }.bind(that), that.delay * i);
                    ++i;
                } else {
                    $child.addClass(that.visibleClass);
                }
            }
        });
    }
}
