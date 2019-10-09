import web from '@sulu/web';
import $ from 'jquery';
import './polyfill';
import WindowScroll from '@sulu/web/packages/components/window-scroll/window-scroll';
import NavigationToggler from './components/NavigationToggler';

window.$ = window.jQuery = $;
window.web = web;

web.registerComponent('navigation-toggler', NavigationToggler);
web.registerComponent('window-scroll', WindowScroll);

if (window.webComponents) {
    web.startComponents(window.webComponents);
}

if (window.webServices) {
    web.callServices(window.webServices);
}
