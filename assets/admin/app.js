// Add project specific javascript code and import of additional bundles here:
import {listToolbarActionRegistry} from 'sulu-admin-bundle/views';
import AlertNamesToolbarAction from "./listToolbarActions/AlertNamesToolbarAction";

listToolbarActionRegistry.add('app.alert_names', AlertNamesToolbarAction);
