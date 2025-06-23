import {Registry} from "../container/index.ts";
import {Render} from "../../interfaces/render.ts";
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
    create(type, options): Element|null {
        const builder = this.createBuilder(type, options);

        if (!builder) {
            return null;
        }

        return builder.create();
    }

    /**
     * Создание механизма отрисовки
     *
     * @param type
     * @param options
     *
     * @since 1.2.0
     * @version 1.2.0
     */
    createBuilder(type, options): Render|null {
        const entity = this.#registry.get(type);

        if (!entity) {
            return null;
        }

        return new entity(options);
    }
}