import {SimpleItem} from "../interfaces/render/simpleItem.d.ts";
import {Options} from "../interfaces/render/options/options.d.ts";

export class AbstractItem implements SimpleItem {
    /**
     * Контейнер с данными
     *
     * @private
     *
     * @since 1.2.0
     * @version 1.2.0
     */
    _container: HTMLTableRowElement;

    /**
     * @since 1.2.0
     * @version 1.2.0
     */
    _titleCell: HTMLTableCellElement;

    /**
     * @since 1.2.0
     * @version 1.2.0
     */
    _valueCell: HTMLTableCellElement;

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
     * @since 1.2.0
     * @version 1.2.0
     */
    get titleCell() {
        return this._titleCell;
    }

    /**
     * @since 1.2.0
     * @version 1.2.0
     */
    get valueCell() {
        return this._valueCell;
    }

    /**
     * Получение DOM элемента
     *
     * @since 1.2.0
     * @version 1.2.0
     */
    getContainer(): HTMLTableRowElement {
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
        this._container = document.createElement('tr');

        this._titleCell = document.createElement('td');
        this._titleCell.width = "40%";
        this._titleCell.className = 'adm-detail-content-cell-l';
        this._titleCell.innerText = BX.message(options?.title);

        this._valueCell = document.createElement('td');
        this._valueCell.width = "60%"
        this._valueCell.className = 'adm-detail-content-cell-r';
        this._valueCell.append(this._createValue(options));

        this.getContainer().append(this._titleCell);
        this.getContainer().append(this._valueCell);
    }
}