import $ from 'jquery';

export default class NumberAnimation {
    initialize($el, options) {
        this.$el = $el;
        this.duration = options.duration ? options.duration : 1000;
        this.window = $(window);
        this.finalNumber = this.$el.text();
        this.$el.text(0);
        this.animationStarted = false;

        this.window.on('scroll', this.startAnimation.bind(this));
        this.startAnimation();
    }

    startAnimation() {
        if (this.animationStarted
            || this.window.scrollTop() + this.window.height() - 150 < this.$el.offset().top) {
            return;
        }

        this.animationStarted = true;

        $({
            counter: 0
        }).animate({
            counter: this.finalNumber
        }, {
            duration: this.duration,
            easing: 'swing',
            step: function(counter) {
                this.$el.text(Math.ceil(counter).toLocaleString());
            }.bind(this)
        });
    }
}
