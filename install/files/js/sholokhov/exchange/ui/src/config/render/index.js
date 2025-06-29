import {Input} from '../../render/input.ts';
import {Select} from "../../render/select.ts";
import {EntitySelector} from "../../render/entitySelector.ts";
import {IBlockProperty} from "../../render/iblockProperty.ts";

const Type = {
    Input: 'input',
    Select: 'select',
    EntitySelector: 'entity-selector',
    IBlockProperty: 'iblock-property',
};

const Map = {};

Map[Type.Input] = Input;
Map[Type.Select] = Select;
Map[Type.EntitySelector] = EntitySelector;
Map[Type.IBlockProperty] = IBlockProperty;

export {Type, Map};