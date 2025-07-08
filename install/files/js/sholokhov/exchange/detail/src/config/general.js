import {RenderType} from 'sholokhov.exchange.ui'

export default {
    fields: [
        {
            view: RenderType.Checkbox,
            options: {
                title: 'SHOLOKHOV_EXCHANGE_SETTINGS_ENTITY_UI_GENERAL_TITLE_FIELD_ACTIVE',
                attributes: {
                    name: 'target[active]'
                },
            }
        },
        {
            view: 'hash',
            options: {
                title: 'SHOLOKHOV_EXCHANGE_SETTINGS_ENTITY_UI_GENERAL_TITLE_FIELD_HASH',
                attributes: {
                    name: 'target[hash]'
                },
            }
        },
    ]
}