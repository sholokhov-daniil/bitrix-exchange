import {createResponse} from "@/utils/http/curl";

export const queryField = async (field) => {
    if (!field?.api?.action) {
        return new Promise((resolve) => {
            resolve(createResponse())
        });
    }

    return await BX.ajax.runAction(
        field.api.action,
        {
            method: 'POST',
            data: field.api?.data || {}
        }
    )
}