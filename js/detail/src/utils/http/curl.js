export const curl = (action, parameters = {}) => {
    if (!parameters?.method) {
        parameters.method = 'POST';
    }

    return BX.ajax.runAction(action, parameters)
}

export const createErrorResponse = (data = {}, errors = []) => {
    const response = createResponse(false);
    response.data = data;
    response.errors = errors;

    return response;
}

export const createResponse = (success = true) => ({
    status: success === true ? "success" : "error",
    data: {},
    errors: []
});

export const createError = (message, code = null) => ({
   message: message,
   code: code,
});