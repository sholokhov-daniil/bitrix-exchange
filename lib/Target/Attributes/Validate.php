<?php

namespace Sholokhov\Exchange\Target\Attributes;

use Attribute;

/**
 * Обозначает, что метод отвечает за валидацию обмена
 *
 * @package Attribute
 * @since 1.0.0
 * @version 1.0.0
 */
#[Attribute(Attribute::TARGET_METHOD)]
class Validate
{
}