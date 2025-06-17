/**
 * Ставка контента в DOM дерево на основе ответа контроллера
 *
 * @param {HTMLElement} target DOM элемент в который будет происходить встраивание
 * @param {Object} response Ответ от API битрикса
 */
export function BXHtml(target, response) {
    if (response?.data?.html) {
        BX.html(target, response.data.html);
    }

    if (response?.assets?.css && Array.isArray(response.assets.css)) {
        response.assets.css.forEach(LoadCss);
    }

    if (response?.assets?.js && Array.isArray(response.assets.js)) {
        response.assets.js.forEach(LoadScript);
    }

    if (response?.data?.assets?.string && Array.isArray(response.data.assets.string)) {
        target.innerHTML += response.data.assets.string.join('');
    }
}

/**
 * Подключение сторонних стилей по ссылке
 *
 * @param {String} style
 */
export function LoadCss(style) {
    BX.loadCSS(style);
}

/**
 * Подключение сторонних скриптов по ссылке
 *
 * @param {String} script
 */
export function LoadScript(script) {
    BX.loadScript(script);
}