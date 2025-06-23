import {SimpleItem} from "../../@types/render/Item.d.ts";
import {Options} from "../../@types/render/options/options.d.ts";

export class AbstractItem implements SimpleItem {
    /**
     * Контейнер с данными
     *
     * @private
     *
     * @since 1.2.0
     * @version 1.2.0
     */
    _container: HTMLElement;

    /**
     * Заголовок элемента
     *
     * @private
     *
     * @since 1.2.0
     * @version 1.2.0
     */
    _title: HTMLElement;

    /**
     * Создание контейнера значения
     *
     * @param options
     *
     * @since 1.2.0
     * @version 1.2.0
     */
    _createValue(options: Options): HTMLElement {};

    /**
     * @param options {Options} Конфигурация элемента
     *
     * @since 1.2.0
     * @version 1.2.0
     */
    constructor(options: Options) {
        this._create(options);
    }

    /**
     * DOM элемент заголовка
     *
     * @since 1.2.0
     * @version 1.2.0
     */
    getTitle(): HTMLElement {
        return this._title;
    }

    /**
     * Получение DOM элемента
     *
     * @since 1.2.0
     * @version 1.2.0
     */
    getContainer(): HTMLElement {
        return this._container;
    }

    /**
     * Создание DOM элемента
     *
     * @param options
     * @private
     *
     * @since 1.2.0
     * @version 1.2.0
     */
    _create(options: Options): void {
        this._container = document.createElement('div');

        this._title = this._createTitle(options);
        this._container.append(this._title );

        const valueContainer = document.createElement('div');
        valueContainer.className = 'value';
        valueContainer.append(this._createValue(options));

        this._container.append(valueContainer);
    }

    /**
     * Создание заголовка
     *
     * @param options {Options}
     *
     * @since 1.2.0
     * @version 1.2.0
     */
    _createTitle(options: Options): HTMLDivElement {
        const title = document.createElement('div');
        title.innerText  = options.title || '';
        title.className = 'title';

        return title;
    }
}