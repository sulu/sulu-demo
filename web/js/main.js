$(window).load(function() {
    $('.js-flexslider').flexslider({
        slideshowSpeed: 4000,
        animationDuration: 1100,
        animation: 'slide',
        directionNav: false,
        controlNav: false,
        pausePlay: false
    });
});

$(".js-partner-carousel").slick({

    // normal options...
    infinite: false,

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