import {HTMLOptions} from './options.d.ts';

export interface SelectOptions extends HTMLOptions {
    api: ApiOptions,
    enums: Array<Enumeration>
}

export interface ApiOptions {
    action: string;
    data: {[key: string]: any},
    callback: Function
}
export interface Enumeration {
    name: string,
    value: string,
}