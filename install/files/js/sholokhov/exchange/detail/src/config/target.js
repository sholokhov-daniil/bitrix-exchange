import {RenderType} from 'sholokhov.exchange.ui'
export default {
    fields: [
        {
            view: RenderType.Input,
            options: {
                title: 'ID импорта:',
                attributes: {
                    name: 'target[hash]'
                },
            }
        },
        {
            view: RenderType.Input,
            options: {
                title: 'Активность:',
                attributes: {
                    type: 'checkbox',
                    name: 'target[active]'
                },
            }
        },
        {
            view: RenderType.Input,
            options: {
                title: 'Деактивировать элементы, которые не пришли в импорте:',
                attributes: {
                    type: 'checkbox',
                    name: 'target[deactivate]'
                },
            }
        },
    ]
};