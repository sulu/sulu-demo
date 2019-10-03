import $ from 'jquery';

export default class DrawAnimation {
    initialize($el, options) {
        this.$el = $el;
        this.window = $(window);
        this.$textcontainer = this.$el.parent().find('#' + this.$el.attr('id') + '-textcontainer');
        this.duration = options.duration ? options.duration : 0.25;
        this.delay = options.delay ? options.delay : 0;

        this.$textcontainer.hide(0);
        this.animationStarted = false;

        this.window.on('load', () => {
            const content = this.$el[0].contentDocument;
            this.$path = $('path', content);
            const pathLength = this.$path.get(0).getTotalLength();
            this.$path.css('stroke-dasharray', pathLength);
            this.$path.css('stroke-dashoffset', pathLength);

            $('<style type="text/css">'
                + '@keyframes draw-animation {to {stroke-dashoffset: 0;}} </style>')
                .appendTo($('svg', content));

            this.window.on('resize', this.startAnimation.bind(this));
            this.startAnimation();
        });
    }

    startAnimation() {
        if (this.animationStarted) {
            // We need this to make the circles visible after resizing from tablet to laptop view
            const content = this.$el[0].contentDocument;
            this.$path = $('path', content);
            this.$path.removeClass('hidden');
        } else {
            setTimeout(() => {
                this.animationStarted = true;
                this.$path.removeClass('hidden');
                const value = 'draw-animation ' + this.duration + 's linear forwards';
                this.$path.css('animation', value);

                this.$textcontainer.fadeIn(this.duration * 1000);

                this.$el.css('visibility', 'visible');
            }, this.delay);
        }
    }
}
