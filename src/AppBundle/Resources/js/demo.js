// This is the javascript we need for our project.
// This file will be minimized and the required components will be included through our node packages.
window.jQuery = require('jquery');
require('slick-carousel');

window.Demo = (function($) {
    "use strict";

    return {

        /**
         * @method init
         */
        init: function() {
            this.navigation();
            this.scroll();
            this.startHomeSlider();
            this.startArtistElementSlider();
            this.startDiscographyElementSlider();
            this.startArticleSlider();
            this.startMediaGallerySlider();
            this.scrollArrow();
        },

        /**
         * @method navigation
         * @description handles the animation of the navigation
         */
        navigation: function() {
            var $nav = $('.nav');
            var $navRight = $('.nav-right');
            var $navItems = $('.nav-elem--items');
            var $navButton = $('.nav-elem--button');
            var $navicon = $('.nav-elem--button i');

            $('#js-nav-button').click(function() {
                $('body').toggleClass('no-scroll');

                if ($nav.hasClass('open')) {
                    $nav.removeClass('open');
                    $navItems.fadeToggle(600, 'linear', function() {
                        $navRight.css('height', '50px');
                    });
                    $navButton.fadeTo(300, 0, function() {
                        $navicon.addClass('ion-navicon');
                        $navicon.removeClass('ion-close');
                        $navButton.fadeTo(300, 1);
                    });
                } else {
                    $nav.addClass('open');
                    $navRight.css('height', '100%');
                    $navItems.fadeToggle(600, 'linear');
                    $navButton.fadeTo(300, 0, function() {
                        $navicon.addClass('ion-close');
                        $navicon.removeClass('ion-navicon');
                        $navButton.fadeTo(300, 1);
                    });
                }
            });
        },

        /**
         * @method scroll
         * @description reacts to scroll event and adds class if user is not at the top
         */
        scroll: function() {
            var $window = $(window);
            var $nav = $('.nav');

            $(document).ready(function() {
                var scroll = $window.scrollTop();
                if (scroll >= 10) {
                    $nav.addClass('sticky');
                }
            });

            $window.scroll(function() {
                var scroll = $window.scrollTop();
                if (scroll >= 10) {
                    $nav.addClass('sticky');
                } else {
                    $nav.removeClass('sticky');
                }
            });
        },

        /**
         * @method startHomeSlider
         */
        startHomeSlider: function() {
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

        /**
         * @method startArtistElementSlider
         */
        startArtistElementSlider: function() {
            var $artistSlider = $('.js-artist-element-collection');

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

        /**
         * @method startDiscographyElementSlider
         */
        startDiscographyElementSlider: function() {
            var $discographySlider = $('.js-discography-element-collection');

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

        /**
         * @method startArticleSlider
         */
        startArticleSlider: function() {
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

        /**
         * @method startMediaGallerySlider
         */
        startMediaGallerySlider: function() {
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
        },

        /**
         * @method scrollArrow
         */
        scrollArrow: function() {
            $('.js-arrow').on('click', function() {
                $('html, body').animate({
                    'scrollTop': $('.content-main').offset().top - $('.demobar').height() - $('.nav').height()
                }, 600);
            });
        }
    };
})(jQuery);

// Here we you can find a solution so that js works correctly in live preview.
window.Demo.Preview = (function ($) {
    "use strict";

    return {
        /**
         * @method init
         */
        init: function () {
            // Mutation observer for watching if the live preview is updating the dom and reacting to it.
            // Here you can find information how a mutation observer works:
            // https://developer.mozilla.org/en/docs/Web/API/MutationObserver
            var targetNode = $("#content");
            var MutationObserver = window.MutationObserver || window.WebKitMutationObserver;
            var myObserver = new MutationObserver(this.mutationHandler);
            var obsConfig = {childList: true, characterData: true, attributes: false, subtree: true};

            targetNode.each(function() {
                myObserver.observe(this, obsConfig);
            });
        },

        /**
         * @method mutationHandler
         * @description event handler of mutation observer
         */
        mutationHandler: function(mutations) {
            mutations.forEach(function(mutation) {
                var $target = $(mutation.target);

                if ($target.is('.js-slider') || $target.has('.js-slider')) {
                    Demo.startHomeSlider();
                }
                if ($target.is('.js-artist-element-collection') || $target.has('.js-artist-element-collection')) {
                    Demo.startArtistElementSlider();
                }
                if ($target.is('.js-discography-element-collection') || $target.has('.js-discography-element-collection')) {
                    Demo.startDiscographyElementSlider();
                }
                if ($target.is('.js-article-collection') || $target.has('.js-article-collection')) {
                    Demo.startArticleSlider();
                }
                if ($target.is('.js-media-gallery') || $target.has('.js-media-gallery')) {
                    Demo.startMediaGallerySlider();
                }
            });
        }
    }
})(jQuery);
