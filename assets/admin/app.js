// Add project specific javascript code and import of additional bundles here:
import {listItemActionRegistry} from 'sulu-admin-bundle/views';
import AlertNameItemAction from "./listItemActions/AlertNameItemAction";

listItemActionRegistry.add('app.alert_name', AlertNameItemAction);
