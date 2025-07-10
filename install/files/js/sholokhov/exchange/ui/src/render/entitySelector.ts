import {TagSelector} from 'ui.entity-selector';
import {AbstractItem} from "./abstractItem.ts";
import {EntitySelectorOptions} from '../interfaces/render/options/entitySelector.d.ts'

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

    _values: Array<HTMLElement> = [];

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

        const selectorOptions = {
            ...options?.selector,
        };

        if (!selectorOptions.hasOwnProperty('events')) {
            selectorOptions.events = {};
        }

        if (selectorOptions.events?.onAfterTagAdd) {
            const afterAdd = selectorOptions.events?.onAfterTagAdd;
            selectorOptions.events.onAfterTagAdd = (event) => {
                this._generateHiddenInputs(event.target, options);
                afterAdd(event);
            }
        } else {
            selectorOptions.events.onAfterTagAdd = (event) => this._generateHiddenInputs(event.target, options);
        }

        if (selectorOptions.events?.onAfterTagRemove) {
            const afterRemove = selectorOptions.events?.onAfterTagRemove;
            selectorOptions.events.onAfterTagRemove = (event) => {
                this._generateHiddenInputs(event.target, options);
                afterRemove(event);
            }
        } else {
            selectorOptions.events.onAfterTagRemove = (event) => this._generateHiddenInputs(event.target, options);
        }


        this._selector = new TagSelector(selectorOptions);
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

    /**
     * @param tagSelector {TagSelector}
     * @param options {EntitySelectorOptions}
     *
     * @since 1.2.0
     * @version 1.2.0
     */
    _generateHiddenInputs(tagSelector: TagSelector, options: EntitySelectorOptions) {
        this._values.forEach((input) => input.remove());

        if (!options.name) {
            return;
        }

        tagSelector.getTags().forEach((tag: {id: string}) => {
            const input = document.createElement('input');
            input.name = options.name;
            input.type = 'hidden';
            input.value = tag.id;

            this._values.push(input);
            this.getContainer().append(input);
        });
    }
}