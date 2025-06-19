import {componentView} from "@/utils/component";

export default {
    fields: [
        {
            view: componentView.input,
            title: 'ID импорта:',
            attributes: {
                name: 'general[hash]'
            },
        },
        {
            view: componentView.checkbox,
            title: 'Активность:',
            attributes: {
                name: 'general[active]'
            },
        },
        {
            view: componentView.checkbox,
            title: 'Деактивировать элементы, которые не пришли в импорте:',
            attributes: {
                name: 'general[deactivate]'
            },
        },
    ],
    userFields: [],
}