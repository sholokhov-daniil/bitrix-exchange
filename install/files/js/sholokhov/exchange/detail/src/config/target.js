import {RenderType} from 'sholokhov.exchange.ui'
export default {
    fields: [
        {
            view: RenderType.Input,
            options: {
                title: 'SHOLOKHOV_EXCHANGE_SETTINGS_ENTITY_UI_TITLE_FIELD_HASH',
                attributes: {
                    name: 'target[hash]'
                },
            }
        },
        {
            view: RenderType.Input,
            options: {
                title: 'SHOLOKHOV_EXCHANGE_SETTINGS_ENTITY_UI_TITLE_FIELD_ACTIVE',
                attributes: {
                    type: 'checkbox',
                    name: 'target[active]'
                },
            }
        },
    ]
};