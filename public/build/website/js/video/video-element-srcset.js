// requestAnimationFrame polyfill by Erik Möller. fixes from Paul Irish and Tino Zijdel
// http://paulirish.com/2011/requestanimationframe-for-smart-animating/
// http://my.opera.com/emoller/blog/2011/12/20/requestanimationframe-for-smart-er-animating

// MIT license

(function () {
    var lastTime = 0;
    var vendors = ['ms', 'moz', 'webkit', 'o'];
    for (var x = 0; x < vendors.length && !window.requestAnimationFrame; ++x) {
        window.requestAnimationFrame = window[vendors[x] + 'RequestAnimationFrame'];
        window.cancelAnimationFrame = window[vendors[x] + 'CancelAnimationFrame']
            || window[vendors[x] + 'CancelRequestAnimationFrame'];
    }

    if (!window.requestAnimationFrame)
        window.requestAnimationFrame = function (callback, element) {
            var currTime = new Date().getTime();
            var timeToCall = Math.max(0, 16 - (currTime - lastTime));
            var id = window.setTimeout(function () { callback(currTime + timeToCall); },
                timeToCall);
            lastTime = currTime + timeToCall;
            return id;
        };

    if (!window.cancelAnimationFrame)
        window.cancelAnimationFrame = function (id) {
            clearTimeout(id);
        };
}());

/*! extend.js | (c) 2017 Chris Ferdinandi | MIT License | http://github.com/cferdinandi/extend */
/**
 * Merge two or more objects together.
 * @param {Boolean}  deep     If true, do a deep (or recursive) merge [optional]
 * @param {Object}   objects  The objects to merge together
 * @returns {Object}          Merged values of defaults and options
 */
var extend = function () {

    // Variables
    var extended = {};
    var deep = false;
    var i = 0;
    var length = arguments.length;

    // Check if a deep merge
    if (Object.prototype.toString.call(arguments[0]) === '[object Boolean]') {
        deep = arguments[0];
        i++;
    }

    // Merge the object into the extended object
    var merge = function (obj) {
        for (var prop in obj) {
            if (Object.prototype.hasOwnProperty.call(obj, prop)) {
                // If deep merge and property is an object, merge properties
                if (deep && Object.prototype.toString.call(obj[prop]) === '[object Object]') {
                    extended[prop] = extend(true, extended[prop], obj[prop]);
                } else {
                    extended[prop] = obj[prop];
                }
            }
        }
    };

    // Loop through each object and conduct a merge
    for (; i < length; i++) {
        var obj = arguments[i];
        merge(obj);
    }

    return extended;

};

/**
 * Breakpoint based multiple videos sources for html5 videos
 *
 * Allows the use of multiple videos sources for different breakpoints on HTML5 videos
 *
 * @version    0.2.0
 * @since      0.1.0
 * @access     public
 *
 * @return {void}
 */

 var videoElementSrcset = function () {
    // TODO allow custom videoElement, videoSources through function parameters

    // declare our variables
    var breakpoint, previousBreakpoint, activeVideoBreakpoint, breakpointWeights, autoplay, timeout, videoSourceElement, activeSource, videoSrcSet = '';

    // if videoElement is not provided, get the first video element in the document
    var videoElement = videoElement || document.getElementsByTagName('video')[0];

    // if videoSources is not provided, create an empty object
     var videoSources = videoSources || [];

    // Public APIs
    var publicAPIs = {};

    // Settings
    var settings;

    var defaults = {
        serviceWorker: true,
        autoplay: false,
        breakpointWeights: {
            '"xsmall"': 1,
            '"small"': 2,
            '"medium"': 3,
            '"large"': 4,
            '"xlarge"': 5
        }
    };

    // install Service Worker to better cache management
    var installWorker = function () {
        navigator.serviceWorker.register('./sw-video-cache.js', { scope: './' }).then(function (registration) {
            // Registration was successful
            console.log('ServiceWorker registration successful with scope: ', registration.scope);
        }, function (err) {
            // registration failed :(
            console.log('ServiceWorker registration failed: ', err);
        });
    }

    // Get the current breakpoint
    var getBreakpoint = function () {
        return window.getComputedStyle(document.body, ':before').content;
    };

    autoplay = videoElement.getAttribute('autoplay');
    if ( autoplay && 'false' !== autoplay ){
        settings.autoplay = true;
    }
    // Calculate breakpoint on page load
    breakpoint = previousBreakpoint = activeVideoBreakpoint = getBreakpoint();

    // if there's no video element is found, return and show error on console
    if (0 === videoElement.length) {
        return;
    }

    // if videoSources is an empty object, get all source elements nested inside videoElement
    // if there is no sources, bail and logs to console
    if (!videoSources || 0 === videoSources.length) {


        videoSourceElement = document.querySelector('video source');
        videoElement
        if (0 === videoSourceElement.length) {
            return;
        }

        // get our information from markup
        videoSrcSet = videoSourceElement.getAttribute('data-srcset');

        // if there's no data-srcset info, bail and log
        if (!videoSrcSet || '' === videoSrcSet) {
            return;
        }

        //convert data-srcset to an array
        videoSrcSet = videoSrcSet.split('; ');

        // loops the through our sources
        for (i = 0; i < videoSrcSet.length; ++i) {

            var components = videoSrcSet[i].split(', ');
            videoSources['"' + components[0] + '"'] = [components[1], 'not-loaded'] ;
        }
    }


    // window.resize listener
    var breakpointListener = function () {
        breakpoint = getBreakpoint();
        if ( breakpointWeights[breakpoint] ) {

            // triggers only if breakpoint changed and if the new breakpoint is larger than the one
            // which loaded the active video
            if ( breakpointWeights[breakpoint] !== breakpointWeights[previousBreakpoint] && breakpointWeights[breakpoint] > breakpointWeights[activeVideoBreakpoint] ) {
                publicAPIs.manageSrc( breakpoint);
            }
        }

        previousBreakpoint = breakpoint;
        return;
    }

    // manager the source swap
    publicAPIs.manageSrc = function ( breakpoint ) {

        // what is the active src?
        activeSource = videoSourceElement.getAttribute('src');

        // if activeSource URL is the same URL for this breakpoint, do nothing
        if ( activeSource !== videoSources[breakpoint][0]) {

            // let's change the src of the source element
            videoSourceElement.setAttribute('src', videoSources[breakpoint][0]);
            //  test if is possible avoid a new load by check if was loaded before

            // loads videos
            videoElement.load();

            // let's signal this video is loaded
            // TODO: this should be done only after the video fully loads
            videoSources[breakpoint][1] = 'loaded'

            // play it, monkey!
            if ( settings.autoplay === true ){
                videoElement.play();
            }

            // update activeSource
            activeSource = videoSources[breakpoint][0];

            // activeVideoBreakpoint
            activeVideoBreakpoint = breakpoint;
        }
        return;

    }

    // init method
    publicAPIs.init = function (options) {

        // Feature test
        var supports = 'querySelector' in document && 'addEventListener' in window;
        if (!supports) return;


        // defaults
        // TODO allows overriding of breakpoint weights

        // Merge user options with the defaults
        settings = extend(defaults, options || {});

        // breakpoint weights, used to avoid unnecessaries video loading
        breakpointWeights = settings.breakpointWeights;

        // if the Service Worker is supported, install it to better cache management
        if ( settings.serviceWorker = true && true === defaults.serviceWorker && 'serviceWorker' in navigator) {
            //installWorker();
            //console.log('installWorker');
        }
        if ( videoSources[breakpoint] ) {
            // run the manageSrc once on load
            publicAPIs.manageSrc(breakpoint);
        }

        // Listen for resize events
        // see https://gomakethings.com/debouncing-events-with-requestanimationframe-for-better-performance/
        window.addEventListener('resize', function (event) {

            // If there's a timer, cancel it
            if (timeout) {
                window.cancelAnimationFrame(timeout);
            }

            // Setup the new requestAnimationFrame()
            timeout = window.requestAnimationFrame(function () {

                // Run our scroll listener
                breakpointListener();

            });

        }, false);

    }

    // return ou publis APIs
    return publicAPIs;
}();