// Add project specific javascript code and import of additional bundles here:
import {fieldRegistry} from 'sulu-admin-bundle/containers';
import Currency from "./fields/Currency";

fieldRegistry.add('currency', Currency);
