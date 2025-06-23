<?php

use Sholokhov\Exchange\Bootstrap;
use Sholokhov\Exchange\Helper\Config;

@require_once 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';
@require_once 'events.php';

// Инициализация конфигураций
(new Bootstrap\Configuration)->bootstrap();
$bootstrapIterator = Config::get('bootstrap') ?: [];
foreach ($bootstrapIterator as $entity) {
    if (is_subclass_of($entity, Bootstrap\BootstrapInterface::class)) {
        (new $entity)->bootstrap();
    }
}
unset($entity);