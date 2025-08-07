export const events = {
    before: 'sholokhovExchange:beforeInitDetail'
}

export const sendBefore = (data) => {
    const event = new CustomEvent(events.before, {detail: data});
    document.dispatchEvent(event);
}