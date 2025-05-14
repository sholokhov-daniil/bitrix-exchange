<?php

namespace Sholokhov\BitrixExchange\Repository\Fields;

use Sholokhov\BitrixExchange\Repository\Types\Memory;

/**
 * Хранилище данных о свойствах
 *
 * @method AbstractFieldRepository|null|mixed get(string $id, mixed $default = null)
 * @method AbstractFieldRepository|null current()
 *
 * @version 1.0.0
 */
class FieldContainer extends Memory
{
    private static self $instance;

    private function __construct()
    {
        parent::__construct();
    }

    public static function getInstance(): self
    {
        return self::$instance ??= new self;
    }
}