import {createResponse, curl} from "../http/curl.ts";

export const query = (field): Promise<object> => {
    if (!field?.api?.action) {
        return new Promise((resolve) => {
            resolve(createResponse())
        });
    }

    const parameters = {
        method: 'POST',
        data: field.api?.data || {}
    };

    return new Promise((resolve, reject) => {
        curl(field.api.action, parameters)
            .then(response => resolve(response))
            .catch(response => reject(response));
    });
}