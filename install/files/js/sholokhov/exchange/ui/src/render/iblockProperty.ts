import {Item} from "../interfaces/render/item.d.ts";
import {EntitySelector} from "./entitySelector.ts";
import {TagEvent} from "../interfaces/render/entity-selector/events/tagSelector.d.ts";

/**
 * Селектор выбора свойств определенного инфоблока
 *
 * @internal
 *
 * @since 1.2.0
 * @version 1.2.0
 */
export class IBlockProperty implements Item {
    /**
     * Контейнер списков
     *
     * @since 1.2.0
     * @version 1.2.0
     */
    _container: HTMLElement;

    /**
     * Список инфоблоков
     *
     * @since 1.2.0
     * @version 1.2.0
     */
    _iBlock: EntitySelector;

    /**
     * Список свойств
     *
     * @since 1.2.0
     * @version 1.2.0
     */
    _property: EntitySelector;

    _options: object;

    constructor(options) {
        this._options = options;
    }

    /**
     * Получение контейнера списков
     *
     * @return {HTMLElement}
     *
     * @since 1.2.0
     * @version 1.2.0
     */
    getContainer(): HTMLElement {
        return this._container ??= this._create();
    }

    /**
     * Инициализация контейнера списка свойств
     *
     * @return {HTMLElement}
     *
     * @since 1.2.0
     * @version 1.2.0
     */
    _create(): HTMLElement {
        const container = document.createElement('div');
        container.append(this._createIBlockSelector().getContainer());

        return container;
    }

    /**
     * Выбран инфоблок
     *
     * @return {void}
     * @param event {TagEvent}
     *
     * @since 1.2.0
     * @version 1.2.0
     */
    _iBlockSelected(event: TagEvent): void {
        this._property?.getContainer()?.remove();
        this._createPropertySelector(event.data.tag.id);
        this._container.append(this._property.getContainer());
    }

    /**
     * Удален выбор инфоблока
     *
     * @return {void}
     *
     * @since 1.2.0
     * @version 1.2.0
     */
    _iBlockRemove(event: TagEvent): void {
        if (!event.target?.tags?.length) {
            this._property?.getContainer()?.remove();
        }
    }

    /**
     * Создание селектора инфоблоков
     *
     * @since 1.2.0
     * @version 1.2.0
     */
    _createIBlockSelector(): EntitySelector {
        return this._iBlock = new EntitySelector({
            title: 'SHOLOKHOV_EXCHANGE_SETTINGS_UI_TITLE_RENDER_IBLOCK_SELECT_IBLOCK',
            name: this._options?.iblock?.name,
            selector: {
                multiple: false,
                addButtonCaption: 'SHOLOHKOV_EXCHANGE_UI_ENTITY_SELECTOR_DIALOG_ADD_BUTTON_CAPTION_SELECT',
                dialogOptions: {
                    entities: [
                        {
                            id: 'sholokhov-exchange-iblock',
                            dynamicSearch: true,
                            dynamicLoad: true,
                        }
                    ]
                },
                events: {
                    onAfterTagAdd: (event: TagEvent) => this._iBlockSelected(event),
                    onAfterTagRemove: (event: TagEvent) => this._iBlockRemove(event),
                }
            }
        });
    }

    /**
     * Создание селектора свойств инфоблока
     *
     * @param iblockId {number}
     *
     * @since 1.2.0
     * @version 1.2.0
     */
    _createPropertySelector(iblockId: number = 0): EntitySelector {
        return this._property = new EntitySelector({
            title: 'SHOLOKHOV_EXCHANGE_UI_ENTITY_PROPERTY_SELECTOR',
            name: this._options?.property?.name,
            selector: {
                multiple: false,
                addButtonCaption: 'SHOLOHKOV_EXCHANGE_UI_ENTITY_SELECTOR_DIALOG_ADD_BUTTON_CAPTION_SELECT',
                dialogOptions: {
                    entities: [
                        {
                            id: 'sholokhov-exchange-iblock-property',
                            dynamicSearch: true,
                            dynamicLoad: true,
                            options: {
                                iblockId: iblockId,
                                nameTemplate: '#NAME# (#CODE#)',
                                ...this._options?.property?.api || {}
                            }
                        }
                    ]
                }
            }
        });
    }
}