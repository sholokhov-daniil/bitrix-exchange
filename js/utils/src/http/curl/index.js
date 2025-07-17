export function runAction(action, data, parameters = {}) {
    const options = {
        ...parameters,
        data: data
    };

    console.log(options);

    return BX.ajax.runAction(action, options);
}