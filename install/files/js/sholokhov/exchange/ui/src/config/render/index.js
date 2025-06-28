import {Input} from '../../render/input.ts';
import {Select} from "../../render/select.ts";
import {IBlockSelect} from "../../render/iblockSelect.ts";
import {EntitySelector} from "../../render/entitySelector.ts";

const Type = {
    Input: 'input',
    Select: 'select',
    IBlockSelector: 'iblock-selector',
    EntitySelector: 'entity-selector',
};

const Map = {};

Map[Type.Input] = Input;
Map[Type.Select] = Select;
Map[Type.IBlockSelector] = IBlockSelect;
Map[Type.EntitySelector] = EntitySelector;

export {Type, Map};