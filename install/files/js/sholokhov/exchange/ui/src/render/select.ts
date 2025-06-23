import {query} from "../util/http/field.ts";
import {AbstractItem} from "./abstractItem.ts";
import {Enumeration, SelectOptions} from "../../@types/render/options/select.d.ts";
import {Select as SelectInterface} from '../../@types/render/select.d.ts';

export class Select extends AbstractItem implements SelectInterface {

    /**
     * Список значений
     *
     * @private
     *
     * @since 1.2.0
     * @version 1.2.0
     */
    _select: HTMLSelectElement;

    /**
     * @param option {Option}
     *
     * @since 1.2.0
     * @version 1.2.0
     */
    addOption(option: Option): void {
        this._select.add(option);
    }

    /**
     * Удаление значения по индексу
     *
     * @param index {number}
     *
     * @since 1.2.0
     * @version 1.2.0
     */
    removeOption(index: number): void {
        this._select.options.remove(index);
    }

    /**
     * Удаление всех значений списка
     *
     * @since 1.2.0
     * @version 1.2.0
     */
    removeAllOptions(): void {
        this._select.options.length = 0;
    }

    /**
     * Получение коллекции значений списка
     *
     * @since 1.2.0
     * @version 1.2.0
     */
    getOptions(): HTMLOptionsCollection {
        return this._select.options;
    }

    /**
     * @param index {number}
     *
     * @since 1.2.0
     * @version 1.2.0
     */
    getOption(index: number) {
        return this._select.options.item(index);
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
        this._select.setAttribute(key, value);
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
        for (let name in attributes) {
            this._select.setAttribute(name, attributes[name]);
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
        this._select.getAttributeNames().forEach(name => attributes[name] = this._select.getAttribute(name));
        
        return attributes;
    }
    
    /**
     * Создание контейнера списка значений
     *
     * @private
     *
     * @since 1.2.0
     * @version 1.2.0
     */
    _createValue(options: SelectOptions): HTMLElement {
        this._select = document.createElement('select');
        this._select.add(new Option('-- Выберите значение --', '', true));

        if (typeof options.events === 'object') {
            for (let eventName in options.events) {
                this._select[eventName] = options.events[eventName];
            }
        }

        for(let name in this.attributes) {
            this._select.setAttribute(name, this.attributes[name]);
        }

        query(options)
            .then(response => {
                if (options.api?.callback) {
                    response = options.api.callback(response);
                }

                let enums = [];

                if (Array.isArray(response.data)) {
                    enums = response.data;
                } else if (Array.isArray(options?.enums)) {
                    enums = options.enums;
                }

                enums.forEach((item: Enumeration) => {
                    this.addOption(
                        new Option(item.name, item.value, false)
                    )
                })
            })
            .catch(response => {
                console.error(response);
                alert(`Ошибка получения значений списка "${options.title}"`);
            })

        return this._select;
    }
}