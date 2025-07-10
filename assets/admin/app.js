// Add project specific javascript code and import of additional bundles here:
import {ckeditorPluginRegistry, ckeditorConfigRegistry} from 'sulu-admin-bundle/containers';
import Font from '@ckeditor/ckeditor5-font/src/font';

ckeditorPluginRegistry.add(Font);
ckeditorConfigRegistry.add((config) => ({
    toolbar: [...config.toolbar, 'fontSize', 'fontFamily', 'fontColor', 'fontBackgroundColor'],
}));
