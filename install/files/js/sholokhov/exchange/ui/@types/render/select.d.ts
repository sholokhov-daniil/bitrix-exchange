import {SimpleItem} from "./Item.d.ts";

export interface Select extends SimpleItem {
    /**
     * @param option {Option}
     *
     * @since 1.2.0
     * @version 1.2.0
     */
    addOption(option: Option): void;

    /**
     * @param index {number}
     *
     * @since 1.2.0
     * @version 1.2.0
     */
    removeOption(index: number): void;

    /**
     * Удаление всех значений списка
     *
     * @since 1.2.0
     * @version 1.2.0
     */
    removeAllOptions(): void;

    /**
     * @since 1.2.0
     * @version 1.2.0
     */
    getOptions(): HTMLOptionsCollection;

    /**
     * @param index {number}
     *
     * @since 1.2.0
     * @version 1.2.0
     */
    getOption(index: number): HTMLOptionElement;
}