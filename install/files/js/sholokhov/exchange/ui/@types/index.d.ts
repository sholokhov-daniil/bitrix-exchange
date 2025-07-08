import {Item} from "../src/interfaces/render/item";

declare module 'sholokhov.exchange.ui' {
    class Factory {
        create(type: string|number, options: object): Item|null;
    }

    class Registry {
        has(id: string|number): boolean;
        get(id: string|number): any|null;
        set(id: string|number, value: any): void;
        remove(id: string|number): void;
        ids: Array<number|string>
    }

    type Type = {
        Input: string,
        Checkbox: string,
        Select: string,
        EntitySelector: string,
        IBlockProperty: string,
        UfProperty: string,
    }
}