// Add project specific javascript code and import of additional bundles here:
import {listFieldTransformerRegistry} from 'sulu-admin-bundle/containers';
import ColoredTextFieldTransformer from "./fieldTransformers/ColoredTextFieldTransformer";

listFieldTransformerRegistry.add('colored_text', new ColoredTextFieldTransformer());
