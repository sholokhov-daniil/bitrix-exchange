import {Render} from "../interfaces/render.ts";
import {query} from "../util/http/field.ts";

export class Select implements Render {
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
     * Создание объекта select
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

        container.append(this.#createValue());

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
     * Создание контейнера списка значений
     *
     * @private
     *
     * @since 1.2.0
     * @version 1.2.0
     */
    #createValue(): Element {
        const container = document.createElement('div');
        container.className = 'value';

        const select = document.createElement('select');
        select.add(new Option('-- Выберите значение --', '', true));

        if (typeof this.#options.events === 'object') {
            for (let eventName in this.#options.events) {
                select[eventName] = this.#options.events[eventName];
            }
        }

        for(let name in this.attributes) {
            select.setAttribute(name, this.attributes[name]);
        }

        query(this.#options)
            .then(response => {
                if (this.#options.api?.callback) {
                    response = this.#options.api.callback(response);
                }

                let enums = [];

                if (Array.isArray(response.data)) {
                    enums = response.data;
                } else if (Array.isArray(this.#options?.enums)) {
                    enums = this.#options.enums;
                }

                enums.forEach(item => {
                    const option = new Option(item.name, item.value, false, this.#options?.value === item.value);
                    select.add(option);
                })
            })
            .catch(response => {
                console.error(response);
                alert(`Ошибка получения значений списка "${this.#options.title}"`);
            })

        return select;
    }
}