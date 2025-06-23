/**
 * Хранилище произвольных данных
 *
 * @since 1.2.0
 * @version 1.2.0
 */
export class Registry {
    /**
     * Данные хранилища
     *
     * @type {object}
     * @private
     *
     * @since 1.2.0
     * @version 1.2.0
     */
    #data: object;

    /**
     *
     * @param data {object} Данные хранилища
     *
     * @since 1.2.0
     * @version 1.2.0
     */
    constructor(data: object = {}) {
        this.#data = data;
    }

    /**
     * Проверка наличия значения в контейнере
     *
     * @return {boolean}
     * @param id {string|number}
     *
     * @since 1.2.0
     * @version 1.2.0
     */
    has(id: string|number): boolean {
        return this.get(id) !== null;
    }

    /**
     * Получение значения
     *
     * @return {any}
     * @param id {string|number}
     *
     * @since 1.2.0
     * @version 1.2.0
     */
    get(id: string|number): any|null {
        return this.#data[id] ?? null;
    }

    /**
     * Установить значение
     *
     * @param id {string|number}
     * @param value {any}
     *
     * @since 1.2.0
     * @version 1.2.0
     */
    set(id: string|number, value: any): void {
        this.#data[id] = value;
    }

    /**
     * Удаление значения по ID
     *
     * @param id {string|number}
     *
     * @since 1.2.0
     * @version 1.2.0
     */
    remove(id: string|number): void {
        if (this.has(id)) {
            delete this.#data[id];
        }
    }

    /**
     * Получение доступных ключей
     *
     * @return {Array<number|string>}
     *
     * @since 1.2.0
     * @version 1.2.0
     */
    get ids() {
        return Object.keys(this.#data);
    }
}