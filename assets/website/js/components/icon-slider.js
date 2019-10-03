import 'slick-carousel';

export default class IconSlider {
    initialize($el) {
        this.$el = $el;

        this.$el.slick({
            dots: true,
            infinite: true,
            speed: 1000,
            autoplay: true,
            pauseOnFocus: false,
            pauseOnHover: false,
            autoplaySpeed: 4000,
            arrows: false,
            slidesToShow: 6,
            slidesToScroll: 2,
            responsive: [
                {
                    breakpoint: 1200,
                    settings: {
                        slidesToShow: 6,
                        slidesToScroll: 3
                    }
                },
                {
                    breakpoint: 991,
                    settings: {
                        slidesToShow: 4,
                        slidesToScroll: 2
                    }
                },
                {
                    breakpoint: 767,
                    settings: {
                        slidesToShow: 2,
                        slidesToScroll: 1
                    }
                }
            ]
        });
    }
}
