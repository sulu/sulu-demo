import web from '@sulu/web';
import WindowScroll from '@sulu/web/packages/components/window-scroll/window-scroll';
import $ from 'jquery';
import '../css/main.scss';
import './polyfill';
import NavigationToggler from './components/NavigationToggler';
import SlickSlider from './components/SlickSlider';
import Tabs from './components/Tabs';
import Timeago from './components/Timeago';

window.$ = window.jQuery = $;
window.web = web;

web.registerComponent('timeago', Timeago);
web.registerComponent('slick-slider', SlickSlider);
web.registerComponent('navigation-toggler', NavigationToggler);
web.registerComponent('window-scroll', WindowScroll);
web.registerComponent('tabs', Tabs);

if (window.webComponents) {
    web.startComponents(window.webComponents);
}

if (window.webServices) {
    web.callServices(window.webServices);
}
