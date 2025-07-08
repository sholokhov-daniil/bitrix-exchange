import {Factory, RenderType} from 'sholokhov.exchange.ui';

export class Hash {
    _container: HTMLElement;
    _options: object;

    constructor(options: object) {
        this._options = options;
    }

    getContainer(): HTMLElement {
       return this._container ??= this._create();
    }

    _create(): HTMLElement {
        const input = Factory.create(RenderType.Input, this._options);

        const label = document.createElement('span');
        label.innerText = 'Сгенерировать';
        label.onclick = () => {
            // Отправляем ajax запрос
            BX.ajax.runAction(
                'sholokhov:exchange.SecureController.generateHash'
            )
            input.value = "OPA";
        }

        input.getContainer().append(label);

        return input.getContainer();
    }
}