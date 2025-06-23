import {Render} from "../interfaces/render.ts";

/**
 * @since 1.2.0
 * @version 1.2.0
 */
export class Input implements Render
{
    /**
     * Конфигурация сборщика
     *
     * @private
     *
     * @since 1.2.0
     * @version 1.2.0
     */
    #options: object;

    /**
     * @param options {object}
     *
     * @since 1.2.0
     * @version 1.2.0
     */
    constructor(options: object = {}) {
        this.#options = options;
    }

    /**
     * Создание объекта input
     *
     * @since 1.2.0
     * @version 1.2.0
     */
    create(): Element {
        const container = document.createElement('div');

        const title = this.#createTitle();
        if (title) {
            container.append(title);
        }

        container.append(this.#createInput());

        return container;
    }

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
        const attributes = this.attributes;
        attributes[key] = value;
        this.attributes = attributes;
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
        this.setAttribute('value', value);
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
        this.setAttribute('type', type);
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
        this.#options.attributes = attributes;
    }

    /**
     * Получение доступных атрибутов
     *
     * @since 1.2.0
     * @version 1.2.0
     */
    get attributes(): object {
        return this.#options?.attributes || {};
    }

    /**
     * Создание заголовка
     *
     * @private
     *
     * @since 1.2.0
     * @version 1.2.0
     */
    #createTitle(): Element|null {
        let title = null;

        if (this.#options.title) {
            title = document.createElement('div');
            title.innerText  = this.#options.title;
            title.className = 'title';
        }

        return title;
    }

    /**
     * Создание хранилища данных
     *
     * @private
     *
     * @since 1.2.0
     * @version 1.2.0
     */
    #createInput(): Element {
        const container = document.createElement('div');
        container.className = 'value';

        const input = document.createElement('input');

        if (typeof this.#options.events === 'object') {
            for (let eventName in this.#options.events) {
                input[eventName] = this.#options.events[eventName];
            }
        }

        for(let name in this.attributes) {
            input.setAttribute(name, this.attributes[name]);
        }

        container.append(input);

        return container;
    }
}