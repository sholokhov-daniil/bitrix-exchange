export interface HTMLOptions extends Options {
    attributes: {[key: string]: string}|null;
    events: {[key: string]: Function|string}|null;
}

export interface Options {
    title: string|null,
}