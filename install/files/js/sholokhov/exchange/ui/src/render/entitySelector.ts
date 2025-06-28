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

        console.log(options.selector);

        if (options?.selector?.addButtonCaption) {
            options.selector.addButtonCaption = BX.message(options.selector.addButtonCaption) || options.selector.addButtonCaption;
        }

        this._selector = new TagSelector(options.selector || {});
        this._selector.renderTo(container);

        // TODO: Вот тут думаю нужно будет бросить событие, а может и не нужно :)

        return container;
    }
}