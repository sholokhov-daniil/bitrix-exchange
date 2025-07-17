import CheckBox from '../../components/form/checkbox/checkbox-field.vue';
import Input from '../../components/form/text/input-field.vue';
import Select from '../../components/form/select/select-field.vue';
import EntitySelector from '../../components/form/select/entity-selector-field.vue';
import IBlockPropertySelector from "../../components/form/select/iblock-property-selector.vue";

export const getFormComponent = (view) => {
    let component = null;

    switch (view) {
        case 'input':
            component = Input;
            break;
        case 'checkbox':
            component = CheckBox;
            break;
        case 'select':
            component = Select;
            break;
        case 'entity-selector':
            component = EntitySelector;
            break;
        case 'iblock-property':
            component = IBlockPropertySelector;
            break;
    }

    return component;
}