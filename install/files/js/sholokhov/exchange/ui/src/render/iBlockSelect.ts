import {Render} from "../interfaces/render.ts";
import {Factory, RenderType} from 'sholokhov.exchange.ui';

/**
 * Генератор списка выбора инфоблока
 *
 * @since 1.2.0
 * @version 1.2.0
 */
export class IBlockSelect implements Render {
    /**
     * Селектор элементов инфоблока
     *
     * @private
     *
     * @since 1.2.0
     * @version 1.2.0
     */
    #iBlockSelect: HTMLSelectElement|null = null;

    /**
     * Создание контейнера списков
     *
     * @since 1.2.0
     * @version 1.2.0
     */
    create(): Element {
        const node = document.createElement('div');
        node.append(
            Factory.create(
                RenderType.Select,
                {
                    title: 'Тип инфоблока: ',
                    attributes: {
                        name: 'target[iblock_type]'
                    },
                    api: {
                        action: 'sholokhov:exchange.IBlockController.getTypes',
                        data: {},
                        callback: this.#normalizeTypeResponse
                    },
                    events: {
                        onchange: (event) => this.#selectedType(event)
                    }
                }
            )
        );

        const iBlockNode = Factory.create(
            RenderType.Select,
            {
                title: 'Инфоблок: ',
                attributes: {
                    name: 'target[iblock_id]'
                }
            }
        );

        this.#iBlockSelect = iBlockNode.querySelector('select');

        node.append(iBlockNode);

        return node;
    }

    /**
     * Выбран тип ИБ
     *
     * @param event
     * @private
     *
     * @since 1.2.0
     * @version 1.2.0
     */
    #selectedType(event): void {
        for(let index = 0; index < this.#iBlockSelect.options.length; index++) {
            if (this.#iBlockSelect.options[index].value) {
                this.#iBlockSelect.options[index].remove();
            }
        }

        if (!event.target.value) {
            return;
        }

        BX.ajax.runAction(
            'sholokhov:exchange.IBlockController.getIBlocks',
            {
                method: 'POST',
                data: {
                    parameters: {
                        filter: {
                            IBLOCK_TYPE_ID: event.target.value
                        }
                    }
                }
            }
        )
            .then(response => {
                if (Array.isArray(response.data)) {
                    response.data.forEach(item => {
                        this.#iBlockSelect.add(new Option(item.name, item.id))
                    })
                }
            })
            .catch(() => alert('Ошибка загрузка получения инфоблоков'))
    }

    /**
     * Нормализация ответа API на получение доступных типов ИБ
     *
     * @param response
     * @private
     *
     * @since 1.2.0
     * @version 1.2.0
     */
    #normalizeTypeResponse(response): object {
        if (Array.isArray(response.data)) {
            response.data = response.data.map(function(iBlock) {
                return {
                    value: iBlock.id,
                    name: iBlock.name
                };
            })
        }

        return response;
    }
}