import web from '@sulu/web';
import $ from 'jquery';
import './polyfill';
import WindowScroll from '@sulu/web/packages/components/window-scroll/window-scroll';
import Timeago from './components/Timeago';
import NavigationToggler from './components/NavigationToggler';
import SlickSlider from './components/SlickSlider';

window.$ = window.jQuery = $;
window.web = web;

web.registerComponent('timeago', Timeago);
web.registerComponent('slick-slider', SlickSlider);
web.registerComponent('navigation-toggler', NavigationToggler);
web.registerComponent('window-scroll', WindowScroll);

if (window.webComponents) {
    web.startComponents(window.webComponents);
}

if (window.webServices) {
    web.callServices(window.webServices);
}
