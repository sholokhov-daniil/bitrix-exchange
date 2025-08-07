export class EntityRegistry {
    _ui = {};

    /**
     * Получение пользовательского интерфейса
     *
     * @param id
     * @returns {*}
     *
     * @since 1.2.0
     * @version 1.2.0
     */
    get(id) {
        return this._ui[id];
    }

    /**
     * Получение всех представлений
     *
     * @returns {{}}
     * @since 1.2.0
     * @version 1.2.0
     */
    getAll() {
        return this._ui;
    }

    /**
     * Указание пользовательского представления
     *
     * @param id
     * @param store
     *
     * @since 1.2.0
     * @version 1.2.0
     */
    set(id, store) {
        this._ui[id] = store;
    }

    /**
     * Проверка наличия представления
     *
     * @param id
     * @returns {boolean}
     *
     * @since 1.2.0
     * @version 1.2.0
     */
    has(id) {
        return Object.hasOwn(this._ui, id);
    }
}