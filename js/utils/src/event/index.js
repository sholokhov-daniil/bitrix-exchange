export const EventManager = {
    emit(id, data) {
        BX.Event.EventEmitter.emit(id, data);
    },
    subscribe(id, callback) {
        BX.Event.EventEmitter.subscribe(id, callback);
    },
};