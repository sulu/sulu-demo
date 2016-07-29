$(".js-slider").slick({
    infinite: true,
    arrows: false,
    autoplay: true,
    autoplaySpeed: 10000
});

$(".js-partner-carousel").slick({
    // normal options
    infinite: true,

    // responsive
    responsive: [{
        breakpoint: 1024,
        settings: {
            slidesToShow: 3,
            slidesToScroll: 3,
            dots: true
        }
    }, {
        breakpoint: 600,
        settings: {
            slidesToShow: 2,
            slidesToScroll: 2,
            dots: true
        }
    }, {
        breakpoint: 400,
        settings: {
            slidesToShow: 1,
            slidesToScroll: 1,
            dots: true
        }
    }]
});

$(".js-services-carousel").slick({
    infinite: true,
    prevArrow: '<i class="ion-ios-arrow-back services-carousel-item-prev"></i>',
    nextArrow: '<i class="ion-ios-arrow-forward services-carousel-item-next"></i>'
});

jQuery(document).ready(function() {
    jQuery('.about-history-tabs-links a').on('click', function(e)  {
        var currentAttrValue = jQuery(this).attr('href');

        // Show/Hide Tabs
        jQuery('.about-history-tabs-content ' + currentAttrValue).show().siblings().hide();

        // Change/remove current tab to active
        jQuery(this).parent('li').addClass('active').siblings().removeClass('active');

        e.preventDefault();
    });
});

var waypoints = $('.js-numbers').waypoint({
    handler: function(direction) {
        $('.js-number-mic').countTo({
            from: 0,
            to: 5894
        });

        $('.js-number-world').countTo({
            from: 0,
            to: 123
        });

        $('.js-number-songs').countTo({
            from: 0,
            to: 13632
        });

        $('.js-number-twitter').countTo({
            from: 0,
            to: 300000
        });
        
        this.destroy();
    },
    offset: 'bottom-in-view'
})

