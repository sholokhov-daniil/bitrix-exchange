import {Factory} from 'sholokhov.exchange.ui';

export class TargetTypeSelector {
    create() {
        const select = Factory.create(
            'select',
            {
                title: 'Тип обмена: ',
                type: 'hidden',
                attributes: {
                    name: 'general[type]',
                },
                api: {
                    action: 'sholokhov:exchange.EntityController.getByType',
                    data: {
                        code: 'target'
                    },
                    callback: function(response) {
                        if (!Array.isArray(response.data)) {
                            return [];
                        }

                        response.data = response.data.map(field => ({
                            value: field.CODE,
                            name: field.NAME,
                        }));

                        return response;
                    }
                }
            }
        );

        // Навешать события

        return select;
    }
}