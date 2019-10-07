import web from 'massive-web';
import $ from 'jquery';
import './polyfill';

window.$ = window.jQuery = $;
window.web = web;

if (window.webComponents) {
    web.startComponents(window.webComponents);
}

if (window.webServices) {
    web.callServices(window.webServices);
}
