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
