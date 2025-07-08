export function normalizeTypeResponse(response: object): object {
    if (!Array.isArray(response.data)) {
        return [];
    }

    response.data = response.data.map(field => ({
        value: field.CODE,
        name: field.NAME,
    }));

    return response;
}