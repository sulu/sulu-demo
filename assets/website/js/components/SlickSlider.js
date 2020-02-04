import $ from 'jquery';
import 'slick-carousel';
import breakpoint from '../services/breakpoint';

export default class SlickSlider {
    $el = null;

    initialize = (el) => {
        this.$el = $(el);

        this.$el.slick({
            dots: false,
            infinite: false,
            speed: 300,
            slidesToShow: 4,
            slidesToScroll: 4,
            arrows: false,
            draggable: false,
            responsive: [
                {
                    breakpoint: breakpoint.tablet + 1,
                    settings: {
                        slidesToShow: 2,
                        slidesToScroll: 2,
                        draggable: true,
                        dots: true,
                    },
                },
                {
                    breakpoint: breakpoint.mobile + 1,
                    settings: {
                        slidesToShow: 1,
                        slidesToScroll: 1,
                        draggable: true,
                        dots: true,
                    },
                },
            ],
        });
    };
}
