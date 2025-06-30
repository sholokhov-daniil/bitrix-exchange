import Config from '../config/general.js';
import {Factory} from 'sholokhov.exchange.ui';

export class General {
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
     * Конфигурация отрисовки
     *
     * @private
     *
     * @since 1.2.0
     * @version 1.2.0
     */
    _options: object;

    /**
     * @param node
     * @param options
     *
     * @since 1.2.0
     * @version 1.2.0
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
     * @since 1.2.0
     * @version 1.2.0
     */
    view(): void {
        this._node.innerHTML = '';
        this._appendFields(this._node, Config.fields);
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