import Config from '../config/target.js';
import {Factory, RenderType} from 'sholokhov.exchange.ui';

/**
 * @since 1.2.0
 * @version 1.2.0
 */
export class Target {
    /**
     * Контейнер UI
     *
     * @private
     *
     * @since 1.2.0
     * @version 1.2.0
     */
    _node: Element|null = null;

    /**
     * Контейнер хранения пользовательских полей
     *
     * @private
     *
     * @since 1.2.0
     * @version 1.2.0
     */
    _customFieldNode: Element|null = null;

    /**
     * Конфигурация отрисовки
     *
     * @private
     *
     * @since 1.2.0
     * @version 1.2.0
     */
    _options: object;

    /**
     * @param {Element|string} node Контейнер в который будет производиться отрисовка
     * @param {object} options Конфигурация отрисовки
     */
    constructor(node: string|Element, options: object = {}) {
        if (typeof node === 'string') {
            this._node = document.querySelector(node);
        } else if (node){
            this._node = node;
        }

        if (!this._node) {
            throw 'Invalid target settings node';
        }

        this._options = options;
    }

    /**
     * Отрисовка контейнера UI настроек
     *
     * @return void
     *
     * @since 1.2.0
     * @version 1.2.0
     */
    view(): void {
        this._node.innerHTML = '';
        this._appendType();
        this._appendFields(this._node, Config.fields);
        this._appendCustomFields();
    }

    /**
     * Загрузка пользовательских полей
     *
     * @param target
     * @private
     *
     * @since 1.2.0
     * @version 1.2.0
     */
    _loadFields(target) {
        BX.ajax.runAction(
            'sholokhov:exchange.EntityController.getFields',
            {
                data: {
                    code: target
                }
            }
        )
            .then(response => {
                this._customFieldNode.innerHTML = '';
                if (Array.isArray(response.data)) {
                    this._appendFields(this._customFieldNode, response.data);
                }
            })
            .catch(response => console.error(response))
    }

    /**
     * Добавление списка доступных обменов
     *
     * @private
     * @return void
     *
     * @since 1.2.0
     * @version 1.2.0
     */
    _appendType(): void {
        let view;
        let options = {
            title: 'SHOLOKHOV_EXCHANGE_SETTINGS_ENTITY_UI_TARGET_TITLE_FIELD_TYPE',
            attributes: {
                name: 'target[type]',
            },
            events: {
                onchange: (event) => this._loadFields(event.target.value),
            }
        };

        if (this._options.id) {
            view = RenderType.Input;
            options.attributes.type = 'hidden';

        } else {
            view = RenderType.Select;
            options.api = {
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
            };
        }

        this._node.append(Factory.create(view, options).getContainer());
    }

    /**
     * Добавление контейнера с пользовательскими полями
     *
     * @private
     *
     * @since 1.2.0
     * @version 1.2.0
     */
    _appendCustomFields(): void {
        this._customFieldNode = document.createElement('div');
        this._node.append(this._customFieldNode);
    }

    /**
     * Отрисовка полей в контейнер
     *
     * @param node
     * @param iterator
     * @private
     *
     * @since 1.2.0
     * @version 1.2.0
     */
    _appendFields(node: Element, iterator: Array<object>): void {
        iterator.forEach(field => {
            const element = Factory.create(field.view, field.options);
            if (element) {
                node.append(element.getContainer());
            }
        })
    }
}