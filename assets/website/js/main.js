import web from 'massive-web';
import $ from 'jquery';
import './polyfill';
import NavigationToggler from './components/NavigationToggler';

window.$ = window.jQuery = $;
window.web = web;

web.registerComponent('navigation-toggler', NavigationToggler);

if (window.webComponents) {
    web.startComponents(window.webComponents);
}

if (window.webServices) {
    web.callServices(window.webServices);
}
