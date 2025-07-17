export function runAction(action, data, parameters = {}) {
    const options = {
        ...parameters,
        data: data
    };

    return BX.ajax.runAction(action, options);
}