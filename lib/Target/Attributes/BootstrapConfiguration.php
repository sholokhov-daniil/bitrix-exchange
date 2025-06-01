<?php

namespace Sholokhov\Exchange\Target\Attributes;

use Attribute;

/**
 * Отвечает за автоматическую загрузку(конфигурацию обмена)
 *
 * @package Attribute
 * @since 1.0.0
 * @version 1.0.0
 */
#[Attribute(Attribute::TARGET_METHOD | Attribute::TARGET_PROPERTY)]
class BootstrapConfiguration
{
}