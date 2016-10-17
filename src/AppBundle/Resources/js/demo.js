window.jQuery = require('jquery');
require('slick-carousel');

window.Demo = (function ($) {
    "use strict";

    return {
        init: function () {
            this.navigation();
            this.scroll();
            this.startHomeSlider();
            this.startArtistElementSlider();
            this.startDiscographyElementSlider();
            this.startArticleSlider();
            this.startMediaGallerySlider();
        },

        navigation: function () {
            var $nav = $('.nav');
            var $navItems = $('.nav-elem--items');
            var $navAdmin = $('.nav-elem--admin');
            var $navButton = $('.nav-elem--button');
            var $navicon = $('.nav-elem--button i');

            $("#js-nav-button").click(function () {
                $('body').toggleClass('no-scroll');
                //TODO: Change values for animation
                if ($nav.hasClass('open')) {
                    $nav.removeClass('open');
                    $navAdmin.fadeToggle(1000, "linear");
                    $navItems.fadeToggle(1000, "linear");
                    $navButton.fadeTo(500, 0, function () {
                        $navicon.addClass('ion-navicon').css('font-size', '38px');
                        $navicon.removeClass('ion-close');
                        $navButton.fadeTo(500, 1);
                    });
                } else {
                    $nav.addClass('open');
                    $navAdmin.fadeToggle(1000, "linear");
                    $navItems.fadeToggle(1000, "linear");
                    $navButton.fadeTo(500, 0, function () {
                        $navicon.addClass('ion-close').css('font-size', '28px');
                        $navicon.removeClass('ion-navicon');
                        $navButton.fadeTo(500, 1);
                    });
                }
            });
        },

        scroll: function () {
            var $window = $(window);
            var $nav = $('.nav');

            $(document).ready(function(){
                var scroll = $window.scrollTop();
                if (scroll >= 10) {
                    $nav.addClass("sticky");
                }
            });

            $window.scroll(function () {
                var scroll = $window.scrollTop();
                if (scroll >= 10) {
                    $nav.addClass("sticky");
                } else {
                    $nav.removeClass("sticky");
                }
            });
        },

        startHomeSlider: function () {
            var $homeSlider = $('.js-slider');

            if (!$homeSlider.length) {
                return;
            }

            $homeSlider.not('.slick-initialized').slick({
                infinite: true,
                arrows: false,
                autoplay: true,
                autoplaySpeed: 10000
            });
        },

        startArtistElementSlider: function () {
            var $artistSlider = $('.js-artist--element-collection');

            if (!$artistSlider.length) {
                return;
            }

            $artistSlider.not('.slick-initialized').slick({
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
        },

        startDiscographyElementSlider: function () {
            var $discographySlider = $('.js-discography--element-collection');

            if (!$discographySlider.length) {
                return;
            }

            $discographySlider.not('.slick-initialized').slick({
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
        },

        startArticleSlider: function () {
            var $articleSlider = $('.js-article-collection');

            if (!$articleSlider.length) {
                return;
            }

            $articleSlider.not('.slick-initialized').slick({
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
        },

        startMediaGallerySlider: function () {
            var $mediaGallery = $('.js-media-gallery');

            if (!$mediaGallery.length) {
                return;
            }

            $mediaGallery.not('.slick-initialized').slick({
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
        }
    };
})(jQuery);

window.Demo.Preview = (function ($) {
    "use strict";

    return {
        init: function () {
            // mutation observer for watching if the live preview is updating the dom and reacting to it
            var targetNode = $("#content");
            var MutationObserver = window.MutationObserver || window.WebKitMutationObserver;
            var myObserver = new MutationObserver(this.mutationHandler);
            var obsConfig = {childList: true, characterData: true, attributes: false, subtree: true};

            targetNode.each(function () {
                myObserver.observe(this, obsConfig);
            });
        },

        mutationHandler: function (mutations) {
            mutations.forEach(function (mutation) {
                var $target = $(mutation.target);

                if ($target.is(".js-slider") || $target.has('.js-slider')) {
                    Demo.startHomeSlider();
                }
                if ($target.is(".js-artist--element-collection") || $target.has('.js-artist--element-collection')) {
                    Demo.startArtistElementSlider();
                }
                if ($target.is(".js-discography--element-collection") || $target.has('.js-discography--element-collection')) {
                    Demo.startDiscographyElementSlider();
                }
                if ($target.is(".js-article-collection") || $target.has('.js-article-collection')) {
                    Demo.startArticleSlider();
                }
                if ($target.is(".js-media-gallery") || $target.has('.js-media-gallery')) {
                    Demo.startMediaGallerySlider();
                }
            });
        }
    }
})(jQuery);

