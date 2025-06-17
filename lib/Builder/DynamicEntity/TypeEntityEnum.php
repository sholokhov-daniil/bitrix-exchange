<?php

namespace Sholokhov\Exchange\Builder\DynamicEntity;

/**
 * Системные типы сущностей
 *
 * @since 1.2.0
 * @version 1.2.0
 */
enum TypeEntityEnum: string
{
    /**
     * Сущность принадлежащей группе источников данных
     *
     * @since 1.2.0
     * @version 1.2.0
     */
    case Source = 'source';

    /**
     * Сущность принадлежащей группе обменов
     *
     * @since 1.2.0
     * @version 1.2.0
     */
    case Target = 'target';

    /**
     * Свойство описывающее карту обмена
     *
     * @since 1.2.0
     * @version 1.2.0
     */
    case Mapping = 'mapping';
}