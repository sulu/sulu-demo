import $ from 'jquery';

export default class Sticky {
    initialize($el, options) {
        this.$el = $el;
        this.$stickyContainer = options.container ? $('#' + options.container) : $el.parent();
        this.currentState = 'static';
        this.offsetTop = 60;
        $(window).scroll(this.checkPosition.bind(this));
        $(window).resize(function() {
            this.checkWidth();
            this.checkPosition();
        }.bind(this));
        this.checkWidth();
        this.checkPosition();
    }

    checkWidth() {
        // Do nothing when element is static
        if ('static' === this.$el.css('position')) {
            return;
        }

        this.$el.width(this.$el.parent().width());
    }

    checkPosition() {
        // Do nothing when element is static
        if ('static' === this.$el.css('position')) {
            this.currentState = '';

            return;
        }

        let scrollTop = $(window).scrollTop();
        let containerOffsetTop = this.$stickyContainer.offset().top - this.offsetTop;
        let containerOffsetHeight = this.$stickyContainer.height();
        let elementHeight = this.$el.height();
        let state = 'static';

        if ((scrollTop + elementHeight) >= (containerOffsetTop + containerOffsetHeight)) {
            state = 'bottom';
        } else if (scrollTop >= containerOffsetTop) {
            state = 'sticky';
        }

        if (this.currentState === state) {
            return;
        }

        this.currentState = state;

        if (state === 'bottom') {
            this.setBottom(elementHeight, containerOffsetHeight);
        } else if (state === 'sticky') {
            this.setSticky();
        } else {
            this.setTop();
        }
    }

    setBottom(elementHeight, containerOffsetHeight) {
        this.$el.css({
            position: 'absolute',
            top: containerOffsetHeight - elementHeight
        });
    }

    setSticky() {
        this.$el.css({
            position: 'fixed',
            top: this.offsetTop
        });
    }

    setTop() {
        this.$el.css({
            position: 'absolute',
            top: 0
        });
    }
}
