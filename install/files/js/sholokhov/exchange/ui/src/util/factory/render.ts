import {Registry} from "../container/index.ts";
import {Item} from "../../../@types/render/Item.d.ts";
import {RenderRegistry} from "../builder/RenderRegistry.ts";

/**
 * @since 1.2.0
 * @version 1.2.0
 */
export default class {
    /**
     * Хранилище сборщиков интерфейса
     *
     * @private
     *
     * @since 1.2.0
     * @version 1.2.0
     */
    #registry: Registry;

    /**
     * @since 1.2.0
     * @version 1.2.0
     */
    constructor() {
        this.#registry = RenderRegistry.create();
    }

    /**
     * Создание DOM элемента
     *
     * @param type
     * @param options
     *
     * @since 1.2.0
     * @version 1.2.0
     */
    create(type, options): Item|null {
        const item = this.#registry.get(type);

        if (!item) {
            return null;
        }

        return new item(options);
    }
}