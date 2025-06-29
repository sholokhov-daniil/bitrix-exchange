export interface Item {
    getContainer(): HTMLElement;
}

export interface SimpleItem extends Item {
    value: any;

    /**
     * Указание атрибута
     *
     * @param key {string}
     * @param value {any}
     *
     * @since 1.2.0
     * @version 1.2.0
     */
    setAttribute(key: string, value: any): void;
}