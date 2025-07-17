export function getMessage(id, replace) {
    return hasMessage(id) ? BX.Loc.getMessage(id, replace) : '';
}

export function hasMessage(id) {
    return BX.Loc.hasMessage(id);
}