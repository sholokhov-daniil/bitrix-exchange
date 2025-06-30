export interface Options {
    container?: ContainerOptions;
}

export interface ContainerOptions {
    general?: string;
    target?: string,
    source?: string,
    map?: string;
}