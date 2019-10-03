import web from 'massive-web';
import $ from 'jquery';
import Expand from 'massive-web/src/components/expand';
import FeatureList from './components/feature-list';
import Slider from './components/slider';
import Sticky from './components/sticky';
import CountryFilter from './components/country-filter';
import CookieBox from './components/cookie-box';
import MenuColorChanger from './components/menu-color-changer';
import BoxAnchor from './components/box-anchor';
import GallerySlider from './components/gallery-slider';
import IconSlider from './components/icon-slider';
import NumberAnimation from './components/number-animation';
import Reveal from './components/reveal';
import DrawAnimation from './components/draw-animation';
import SlideIn from './components/slide-in';
import './polyfill';

window.$ = window.jQuery = $;
window.web = web;

web.registerComponent('expand', Expand);
web.registerComponent('slider', Slider);
web.registerComponent('feature-list', FeatureList);
web.registerComponent('sticky', Sticky);
web.registerComponent('country-filter', CountryFilter);
web.registerComponent('cookie-box', CookieBox);
web.registerComponent('menu-color-changer', MenuColorChanger);
web.registerComponent('box-anchor', BoxAnchor);
web.registerComponent('gallery-slider', GallerySlider);
web.registerComponent('reveal', Reveal);
web.registerComponent('icon-slider', IconSlider);
web.registerComponent('number-animation', NumberAnimation);
web.registerComponent('draw-animation', DrawAnimation);
web.registerComponent('slide-in', SlideIn);

if (window.webComponents) {
    web.startComponents(window.webComponents);
}

if (window.webServices) {
    web.callServices(window.webServices);
}
