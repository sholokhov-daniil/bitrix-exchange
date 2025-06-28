import {Item} from "../../@types/render/Item.d.ts";
import {Factory, RenderType} from 'sholokhov.exchange.ui';
import {Select} from "../../@types/render/select.d.ts";

/**
 * Генератор списка выбора инфоблока
 *
 * @since 1.2.0
 * @version 1.2.0
 */
export class IBlockSelect implements Item {
    /**
     * @private
     * 
     * @since 1.2.0
     * @version 1.2.0
     */
    _container: HTMLElement;

    /**
     * @private
     * 
     * @since 1.2.0
     * @version 1.2.0
     */
    _type: Select;

    /**
     * @private
     *
     * @since 1.2.0
     * @version 1.2.0
     */
    _iBlock: Select;

    /**
     * @since 1.2.0
     * @version 1.2.0
     */
    constructor() {
        this._create();
    }
    
    /**
     * DOM элемент селекторов
     *
     * @since 1.2.0
     * @version 1.2.0
     */
    getContainer(): HTMLElement {
        return this._container;
    }

    /**
     * Создание контейнера списков
     *
     * @since 1.2.0
     * @version 1.2.0
     */
    _create(): void {
        this._container = document.createElement('div');

        this._type = Factory.create(
            RenderType.Select,
            {
                title: 'SHOLOKHOV_EXCHANGE_SETTINGS_UI_TITLE_RENDER_IBLOCK_SELECT_TYPE',
                attributes: {
                    name: 'target[iblock_type]'
                },
                api: {
                    action: 'sholokhov:exchange.IBlockController.getTypes',
                    data: {},
                    callback: this._normalizeTypeResponse
                },
                events: {
                    onchange: (event) => this._selectedType(event)
                }
            }
        )


        this._container.append(this._type.getContainer());

        this._iBlock = Factory.create(
            RenderType.Select,
            {
                title: 'SHOLOKHOV_EXCHANGE_SETTINGS_UI_TITLE_RENDER_IBLOCK_SELECT_IBLOCK',
                attributes: {
                    name: 'target[iblock_id]'
                }
            }
        );

        this._container.append(this._iBlock.getContainer());
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
    _selectedType(event): void {
        const iBlockOptions = this._iBlock.getOptions();

        for(let index = 0; index < iBlockOptions.length; index++) {
            if (iBlockOptions[index].value) {
                this._iBlock.removeOption(index);
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
                    response.data.forEach((item) => {
                        this._iBlock.addOption(new Option(item.name, item.id))
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
    _normalizeTypeResponse(response): object {
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