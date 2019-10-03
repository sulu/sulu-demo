import $ from 'jquery';
import 'slick-carousel';

export default class GallerySlider {
    initialize($el) {
        this.$el = $el;

        this.$el.slick({
            infinite: true,
            autoplay: false,
            prevArrow: '<button type="button" aria-label="prev" class="gallery__slick-prev">'
                + '<span class="icon-arrow-left"></span>'
                + '</button>',
            nextArrow: '<button type="button" aria-label="next" class="gallery__slick-next">'
                + '<span class="icon-arrow-right"></span>'
                + '</button>',
            speed: 300
        });
        this.$pagingContainer = $('<span class="gallery__paging-info"></span>');
        this.$pagingContainer.insertAfter(this.$el);

        this.setPagingInfo();
        $el.on('afterChange', this.setPagingInfo.bind(this));
    }

    setPagingInfo() {
        let slick = this.$el.prop('slick');
        let i = (slick.currentSlide ? slick.currentSlide : 0) + 1;
        this.$pagingContainer.text(i + '/' + slick.slideCount);
    }
}
