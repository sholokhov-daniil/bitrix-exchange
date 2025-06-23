import Config from '../config/target.js';
import {Factory, RenderType} from 'sholokhov.exchange.ui';

/**
 * @since 1.2.0
 * @version 1.2.0
 */
export class TargetSettings {
    /**
     * Контейнер UI
     *
     * @private
     *
     * @since 1.2.0
     * @version 1.2.0
     */
    #node: Element|null = null;

    /**
     * Контейнер хранения пользовательских полей
     *
     * @private
     *
     * @since 1.2.0
     * @version 1.2.0
     */
    #customFieldNode: Element|null = null;

    /**
     * Конфигурация отрисовки
     *
     * @private
     *
     * @since 1.2.0
     * @version 1.2.0
     */
    #options: object;

    /**
     * @param {Element|string} node Контейнер в который будет производиться отрисовка
     * @param {object} options Конфигурация отрисовки
     */
    constructor(node: string|Element, options: object = {}) {
        if (typeof node === 'string') {
            this.#node = document.querySelector(node);
        } else if (node){
            this.#node = node;
        }

        if (!this.#node) {
            throw 'Invalid target settings node';
        }

        this.#options = options;
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
        this.#node.innerHTML = '';
        this.#appendType();
        this.#appendFields(this.#node, Config.fields);
        this.#appendCustomFields();
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
    #loadFields(target) {
        BX.ajax.runAction(
            'sholokhov:exchange.EntityController.getFields',
            {
                data: {
                    code: target
                }
            }
        )
            .then(response => {
                this.#customFieldNode.innerHTML = '';
                if (Array.isArray(response.data)) {
                    this.#appendFields(this.#customFieldNode, response.data);
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
    #appendType(): void {
        let view;
        let options = {
            title: 'Тип обмена:',
            attributes: {
                name: 'target[type]',
            },
            events: {
                onchange: (event) => this.#loadFields(event.target.value),
            }
        };

        if (this.#options.id) {
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

        this.#node.append(Factory.create(view, options));
    }

    /**
     * Добавление контейнера с пользовательскими полями
     *
     * @private
     *
     * @since 1.2.0
     * @version 1.2.0
     */
    #appendCustomFields(): void {
        this.#customFieldNode = document.createElement('div');
        this.#node.append(this.#customFieldNode);
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
    #appendFields(node: Element, iterator: Array<object>): void {
        iterator.forEach(field => {
            const element = Factory.create(field.view, field.options);
            if (element) {
                node.append(element);
            }
        })
    }
}