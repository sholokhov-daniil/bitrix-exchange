export interface Options {
    title: string,
    attributes: {[key: string]: string};
    events: {[key: string]: Function|string}
}