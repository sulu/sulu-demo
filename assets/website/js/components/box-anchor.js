import $ from 'jquery';

export default class BoxAnchor {
    initialize($el, options) {
        this.box = $el;
        this.bottom = options.bottom;

        $(window).scroll(this.checkPosition.bind(this));
        this.checkPosition();
    }

    checkPosition() {
        let contentHeight = $('main').height();
        let scrollTop = $(window).scrollTop();

        if (contentHeight > 0
            && scrollTop + $(window).height() + 70 > contentHeight + this.bottom + this.box.height() / 2) {
            this.setBottomAnchor(this.box, contentHeight);
        } else {
            this.removeBottomAnchor(this.box);
        }
    }

    setBottomAnchor(box, contentHeight) {
        box.css('top', contentHeight - 70 - this.box.height() / 2);
        box.css('bottom', 'initial');
        box.css('position', 'absolute');
    }

    removeBottomAnchor(box) {
        box.css('top', 'initial');
        box.css('bottom', this.bottom + 'px');
        box.css('position', 'fixed');
    }
}
