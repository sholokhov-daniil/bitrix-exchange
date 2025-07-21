export class ExternalRegistry {
    _stores = {};

    /**
     * Получение пользовательского хранилища данных
     *
     * @param id
     * @returns {*}
     *
     * @since 1.2.0
     * @version 1.2.0
     */
    get(id) {
        return this._stores[id];
    }

    /**
     * Получение всех данных
     *
     * @returns {{}}
     * @since 1.2.0
     * @version 1.2.0
     */
    getAll() {
        return this._stores;
    }

    /**
     * Указание пользовательского хранилища данных
     *
     * @param id
     * @param store
     *
     * @since 1.2.0
     * @version 1.2.0
     */
    set(id, store) {
        this._stores[id] = store;
    }

    /**
     * Проверка наличия хранилища данных
     *
     * @param id
     * @returns {boolean}
     *
     * @since 1.2.0
     * @version 1.2.0
     */
    has(id) {
        return Object.hasOwn(this._stores, id);
    }
}