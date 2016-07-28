$(".js-slider").slick({

    // normal options...
    infinite: true,
    arrows: false,
    autoplay:true,
    autoplaySpeed: 10000

});

$(".js-partner-carousel").slick({

    // normal options...
    infinite: true,

    // the magic
    responsive: [{

        breakpoint: 1024,
        settings: {
            slidesToShow: 3,
            dots:true
        }

    }, {

        breakpoint: 600,
        settings: {
            slidesToShow: 2,
            dots: true
        }

    },{

        breakpoint: 400,
        settings: {
            slidesToShow: 1,
            dots: true
        }

    }, {

        breakpoint: 300,
        settings: "unslick" // destroys slick

    }]
});

$(".js-services-carousel").slick({

    // normal options...
    infinite: true,
    prevArrow: '<i class="ion-ios-arrow-back services-carousel-item-prev"></i>',
    nextArrow: '<i class="ion-ios-arrow-forward services-carousel-item-next"></i>'

});