var $ = require('jquery');
require('slick-carousel');

$("#js-nav-button").click(function () {
    //TODO: Change values for animation
    if($(".nav").hasClass('open')) {
        $(".nav").removeClass('open');
        $(".nav-elem--admin").fadeToggle(1000, "linear");
        $(".nav-elem--items").fadeToggle(1000, "linear");
        $(".nav-elem--button").fadeTo(500, 0, function () {
            $(".ion-close").addClass('ion-navicon').css('font-size','38px');
            $(".ion-close").removeClass('ion-close');
            $(".nav-elem--button").fadeTo(500, 1);
        });
    } else {
        $(".nav").addClass('open');
        $(".nav-elem--admin").fadeToggle(1000, "linear");
        $(".nav-elem--items").fadeToggle(1000, "linear");
        $(".nav-elem--button").fadeTo(500, 0, function () {
            $(".ion-navicon").addClass('ion-close').css('font-size','28px');
            $(".ion-navicon").removeClass('ion-navicon');
            $(".nav-elem--button").fadeTo(500, 1);
        });
    }
});

$(window).scroll(function() {
    var scroll = $(window).scrollTop();
    if (scroll >= 10) {
        $(".nav").addClass("sticky");
    } else {
        $(".nav").removeClass("sticky");
    }
});


$(".js-slider").slick({
    infinite: true,
    arrows: false,
    autoplay: true,
    autoplaySpeed: 10000
});

$(".js-artist--element-collection").slick({
    infinite: true,
    autoplay: true,
    autoplaySpeed: 5000,
    arrows: false,
    draggable: true,
    responsive: [{
        breakpoint: 1023,
        settings: {
            slidesToShow: 2,
            slidesToScroll: 2,
            dots: true
        }
    }, {
        breakpoint: 480,
        settings: {
            slidesToShow: 1,
            slidesToScroll: 1,
            dots: true
        }
    }]
});

$(".js-discography--element-collection").slick({
    infinite: true,
    autoplay: true,
    autoplaySpeed: 5000,
    arrows: false,
    draggable: true,
    responsive: [{
        breakpoint: 1023,
        settings: {
            slidesToShow: 2,
            slidesToScroll: 2,
            dots: true
        }
    }, {
        breakpoint: 480,
        settings: {
            slidesToShow: 1,
            slidesToScroll: 1,
            dots: true
        }
    }]
});

$(".js-article-collection").slick({
    infinite: true,
    autoplay: true,
    autoplaySpeed: 5000,
    arrows: false,
    draggable: true,
    responsive: [{
        breakpoint: 1023,
        settings: {
            slidesToShow: 2,
            slidesToScroll: 2,
            dots: true
        }
    }, {
        breakpoint: 480,
        settings: {
            slidesToShow: 1,
            slidesToScroll: 1,
            dots: true
        }
    }]
});

$(".js-media-gallery").slick({
    infinite: true,
    autoplay: true,
    autoplaySpeed: 5000,
    arrows: false,
    draggable: true,
    dots: false,
    responsive: [{
        breakpoint: 1023,
        settings: {
            slidesToShow: 2,
            slidesToScroll: 2,
            dots: false
        }
    }, {
        breakpoint: 480,
        settings: {
            slidesToShow: 1,
            slidesToScroll: 1,
            dots: false
        }
    }]
});