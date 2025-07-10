export interface Options {
    container: ContainerOptions;
    signed?: string,
}

export interface ContainerOptions {
    form: string,
    general?: string;
    target?: string,
    source?: string,
    map?: string;
}

export interface HttpParameters {
    data?: object|FormData,
    json?: object,
    navigation?: object,
    analyticsLabel?: string|object,
    method?: string,
}

export interface HttpResponse {
    status: string,
    data: any,
    errors: Array<HttpError>
}

export interface HttpError {
    message: string,
    code: string|number,
}