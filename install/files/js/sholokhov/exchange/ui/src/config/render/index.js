import {Input} from '../../render/input.ts';
import {Select} from "../../render/select.ts";
import {IBlockSelect} from "../../render/iBlockSelect.ts";

const Type = {
    Input: 'input',
    Select: 'select',
    IBlockSelector: 'iblock-selector',
};

const Map = {};

Map[Type.Input] = Input;
Map[Type.Select] = Select;
Map[Type.IBlockSelector] = IBlockSelect;

export {Type, Map};