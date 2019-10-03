import $ from 'jquery';
import 'slick-carousel';

export default class Slider {
    initialize($el) {
        this.$el = $el;

        this.$el.slick({
            infinite: true,
            autoplay: true,
            pauseOnFocus: false,
            pauseOnHover: false,
            autoplaySpeed: 4000,
            prevArrow: '<button type="button" aria-label="prev" class="slick-prev">'
                    + '<span class="icon-arrow-left"></span>'
                + '</button>',
            nextArrow: '<button type="button" aria-label="next" class="slick-next">'
                    + '<span class="icon-arrow-right"></span>'
                + '</button>',
            speed: 1000,
            dots: true,
            customPaging: function(slider, i) {
                let imgSrc = $(slider.$slides[i]).find('[data-slider-image]').attr('data-slider-image');

                if (!imgSrc) {
                    return;
                }

                return '<img src="' + imgSrc + '" alt="Slide ' + i + '" class="slick-dots-image">';
            }
        });
    }
}
