import {Factory, RenderType} from 'sholokhov.exchange.ui';

import Config from '../config/source.js';
import {normalizeTypeResponse} from "../utils/helper.ts";

export class Source {
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
        this._appendTypeField();
        this._appendFields(this._node, Config.fields);
        this._appendCustomFields();
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
     * Добавление списка доступных источников
     *
     * @private
     * @return void
     *
     * @since 1.2.0
     * @version 1.2.0
     */
    _appendTypeField(): void {
        const options = {
            title: 'SHOLOKHOV_EXCHANGE_SETTINGS_ENTITY_UI_SOURCE_TITLE_FIELD_TYPE',
            attributes: {
                name: 'source[type]'
            },
            events: {
                onchange: (event) => this._loadFields(event.target.value)
            },
            api: {
                action: 'sholokhov:exchange.EntityController.getByType',
                data: {
                    code: 'source'
                },
                callback: normalizeTypeResponse,
            }
        };

        this._node.append(Factory.create(RenderType.Select, options).getContainer());
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

    /**
     * Загрузка пользовательских полей
     *
     * @param source {string}
     * @private
     *
     * @since 1.2.0
     * @version 1.2.0
     */
    _loadFields(source: string): void {
        BX.ajax.runAction(
            'sholokhov:exchange.EntityController.getFields',
            {
                data: {
                    code: source
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
}