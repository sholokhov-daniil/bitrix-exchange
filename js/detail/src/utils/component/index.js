import SelectField from '@/components/fields/SelectField.vue';
import CheckboxField from '@/components/fields/CheckboxField.vue';
import ApiField from '@/components/fields/ApiField.vue';
import InputField from '@/components/fields/InputField.vue';

export const componentView = {
    selector: 'selector',
    checkbox: 'checkbox',
    api: 'api',
    input: 'input'
};

export function getComponent(field) {
    let component;

    switch(field.view) {
        case componentView.selector:
            component = SelectField;
            break;
        case componentView.checkbox:
            component = CheckboxField;
            break;
        case componentView.api:
            component = ApiField;
                break;
        case componentView.input:
            component = InputField
            break;
    }

    return component;
}