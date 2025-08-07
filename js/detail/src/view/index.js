export const view = (template, data) => {
    const registry = Sholokhov.Exchange.Detail.entityRegistry;

    if (!registry.has(template)) {
        return;
    }

    const render = registry.get(template);

    render(data);
}

export const registration = (template, callback) => {
    const registry = Sholokhov.Exchange.Detail.entityRegistry;
    registry.set(template, callback);
}