// Polyfills
import 'regenerator-runtime/runtime';

// Bundles
import {startAdmin} from 'sulu-admin-bundle';
import 'sulu-audience-targeting-bundle';
import 'sulu-category-bundle';
import 'sulu-contact-bundle';
import 'sulu-custom-url-bundle';
import 'sulu-location-bundle';
import 'sulu-media-bundle';
import 'sulu-page-bundle';
import 'sulu-preview-bundle';
import 'sulu-route-bundle';
import 'sulu-search-bundle';
import 'sulu-security-bundle';
import 'sulu-snippet-bundle';
import 'sulu-website-bundle';

// Implement custom extensions here
import {ckeditorPluginRegistry, ckeditorConfigRegistry} from 'sulu-admin-bundle/containers';
import Font from '@ckeditor/ckeditor5-font/src/font';

ckeditorPluginRegistry.add(Font);
ckeditorConfigRegistry.add((config) => ({
    toolbar: [...config.toolbar, 'fontSize', 'fontFamily', 'fontColor', 'fontBackgroundColor'],
}));

// Start admin application
startAdmin();

const observer = new MutationObserver(function(mutations) {
    const inputs = document.querySelectorAll('[class^=login-container] input');

    if (inputs.length > 0) {
        const nativeInputValueSetter = Object.getOwnPropertyDescriptor(window.HTMLInputElement.prototype, 'value').set;

        nativeInputValueSetter.call(inputs[0], 'admin');
        const event1 = new Event('input', {bubbles: true});
        inputs[0].dispatchEvent(event1);

        nativeInputValueSetter.call(inputs[1], 'admin');
        const event2 = new Event('input', {bubbles: true});
        inputs[1].dispatchEvent(event2);

        observer.disconnect();
    }
});

observer.observe(document.querySelector('[class^=login-container]'), {childList: true, subtree: true});
