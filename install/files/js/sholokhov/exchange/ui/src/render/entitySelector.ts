import {TagSelector} from 'ui.entity-selector';
import {AbstractItem} from "./abstractItem.ts";
import {EntitySelectorOptions} from '../../@types/render/options/entitySelector.d.ts'

/**
 * Генератор списка элементов сущности
 *
 * @since 1.2.0
 * @version 1.2.0
 */
export class EntitySelector extends AbstractItem {
    /**
     * Объект списка
     *
     * @private
     *
     * @since 1.2.0
     * @version 1.2.0
     */
    _selector: TagSelector;

    constructor(options: EntitySelectorOptions) {
        super(options);
    }

    /**
     * Создание списка сущностей
     *
     * @param options {EntitySelectorOptions}
     *
     * @since 1.2.0
     * @version 1.2.0
     */
    _createValue(options: EntitySelectorOptions): HTMLElement {
        const container = document.createElement('div');

        if (options?.selector?.addButtonCaption) {
            options.selector.addButtonCaption = BX.message(options.selector.addButtonCaption) || options.selector.addButtonCaption;
        }

        this._selector = new TagSelector(options?.selector || {});
        this.selector.renderTo(container);

        return container;
    }

    /**
     * @return {TagSelector}
     * @since 1.2.0
     * @version 1.2.0
     */
    get selector(): TagSelector {
        return this._selector;
    }
}