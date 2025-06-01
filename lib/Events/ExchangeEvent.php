<?php

namespace Sholokhov\Exchange\Events;

use Sholokhov\Exchange\Target\Attributes\Events as Attribute;

/**
 * Зарегистрированные события обмена
 *
 * @since 1.0.0
 * @version 1.0.0
 */
enum ExchangeEvent: string
{
    /**
     * Событие вызывается перед началом обмена
     *
     * @since 1.0.0
     * @version 1.0.0
     */
    case BeforeRun = 'onBeforeRun';

    /**
     * Событие вызывается после завершения обмена
     *
     * @since 1.0.0
     * @version 1.0.0
     */
    case AfterRun = 'onAfterRun';

    /**
     * Событие вызывается перед добавлением элемента сущности
     *
     * @since 1.0.0
     * @version 1.0.0
     */
    case BeforeAdd = 'onBeforeAdd';

    /**
     * Событие вызывается после создания элемента сущности
     *
     * @since 1.0.0
     * @version 1.0.0
     */
    case AfterAdd = 'onAfterAdd';

    /**
     * Событие вызывается перед обновлением элемента сущности
     *
     * @since 1.0.0
     * @version 1.0.0
     */
    case BeforeUpdate = 'onBeforeUpdate';

    /**
     * Событие вызывается после обновления элемента сущности
     *
     * @since 1.0.0
     * @version 1.0.0
     */
    case AfterUpdate = 'onAfterUpdate';

    /**
     * Событий перед импортом значения и его преобразованием
     *
     * @since 1.0.0
     * @version 1.0.0
     */
    case BeforeImportItem = 'onBeforeImportItem';

    /**
     * Событие после импорта значения
     *
     * @since 1.0.0
     * @version 1.0.0
     */
    case AfterImportItem = 'onAfterImportItem';
}