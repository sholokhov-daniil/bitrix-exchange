export const curl = (action: string, parameters: object = {}): BX.Promise => {
    if (!parameters?.method) {
        parameters.method = 'POST';
    }

    return BX.ajax.runAction(action, parameters)
}

export const createErrorResponse = (data: object = {}, errors: Array<object> = []): {status: string, data: object, errors: Array<object>} => {
    const response = createResponse();
    response.status = 'error';
    response.data = data;
    response.errors = errors;

    return response;
}

export const createResponse = (): {status: string, data: object, errors: Array<object>} => ({
    status: "success",
    data: {},
    errors: []
});

export const createError = (message: string, code: string|number = null): {message: string, code: string|number|null} => ({
    message: message,
    code: code,
});