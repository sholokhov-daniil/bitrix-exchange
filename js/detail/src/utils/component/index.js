export function getComponent(field) {
    let component;

    switch(field.type) {
        case 'selector':
            component = import('@/components/fields/SelectField.vue');
            break;
        case 'checkbox':
            component = import('@/components/fields/CheckboxField.vue');
            break;
        default:
            component = field.api ? import('@/components/fields/ApiField.vue') : import('@/components/fields/InputField.vue');
            break;
    }

    return component;
}