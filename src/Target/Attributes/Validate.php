<?php

namespace Sholokhov\BitrixExchange\Target\Attributes;

use Attribute;

/**
 * Флаг, что метод отвечает за валидацию обмена
 *
 * @since 1.0.0
 * @version 1.0.0
 */
#[Attribute(Attribute::TARGET_METHOD)]
class Validate
{
}