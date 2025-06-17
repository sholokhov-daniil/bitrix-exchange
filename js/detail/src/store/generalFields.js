import InputField from "@/components/fields/InputField.vue";
import CheckboxField from "@/components/fields/CheckboxField.vue";

export default {
    fields: [
        {
            component: InputField,
            settings: {
                title: 'ID импорта:',
                attributes: {
                    name: 'general[hash]'
                }
            }
        },
        {
            component: CheckboxField,
            settings: {
                title: 'Активность:',
                attributes: {
                    name: 'general[active]'
                }
            }
        },
        {
            component: CheckboxField,
            settings: {
                title: 'Деактивировать элементы, которые не пришли в импорте:',
                attributes: {
                    name: 'general[deactivate]'
                }
            }
        },
    ],
    userFields: [],
}