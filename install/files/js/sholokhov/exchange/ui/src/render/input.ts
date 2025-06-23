import {AbstractItem} from './abstractItem.ts';
import {Options} from "../../@types/render/options/options.d.ts";

/**
 * @since 1.2.0
 * @version 1.2.0
 */
export class Input extends AbstractItem {
    /**
     * @private
     *
     * @since 1.2.0
     * @version 1.2.0
     */
    _input: HTMLInputElement;

    /**
     * Указание атрибута
     *
     * @param key
     * @param value
     *
     * @since 1.2.0
     * @version 1.2.0
     */
    setAttribute(key: string, value: any): void {
        this._input.setAttribute(key, value);
    }

    /**
     * Указание наименования элемента
     *
     * @param name {string}
     *
     * @since 1.2.0
     * @version 1.2.0
     */
    set name(name: string) {
        this.setAttribute('name', name);
    }

    /**
     * Указание значения инпута
     *
     * @param value {string}
     *
     * @since 1.2.0
     * @version 1.2.0
     */
    set value(value: string) {
        this._input.value = value;
    }

    /**
     * Получение значения
     *
     * @since 1.2.0
     * @version 1.2.0
     */
    get value(): any {
        return this._input.value;
    }

    /**
     * Указание типа значения
     *
     * @param type {string}
     *
     * @since 1.2.0
     * @version 1.2.0
     */
    set type(type: string) {
        this._input.type = type;
    }

    /**
     * Указание атрибутов
     *
     * @param attributes
     *
     * @since 1.2.0
     * @version 1.2.0
     */
    set attributes(attributes: object) {
        for (let name in attributes) {
            this._input.setAttribute(name, attributes[name]);
        }
    }

    /**
     * Получение доступных атрибутов
     *
     * @since 1.2.0
     * @version 1.2.0
     */
    get attributes(): object {
        const attributes = {};
        this._input.getAttributeNames().forEach(name => attributes[name] = this._input.getAttribute(name));

        return attributes;
    }

    /**
     * Создание хранилища данных
     *
     * @private
     *
     * @since 1.2.0
     * @version 1.2.0
     */
    _createValue(options: Options): HTMLElement {
        const container = document.createElement('div');
        container.className = 'value';

        this._input = document.createElement('input');

        if (options.events) {
            for (let eventName in options.events) {
                this._input[eventName] = options.events[eventName];
            }
        }

        if (options.attributes) {
            for (let name in options.attributes) {
                this._input.setAttribute(name, this.attributes[name]);
            }
        }

        container.append(this._input);

        return container;
    }
}