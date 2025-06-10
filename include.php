<?php

use Sholokhov\Exchange\Bootstrap\Configuration;

@require_once 'vendor/autoload.php';
@require_once 'events.php';

// Инициализация конфигураций
$configBootstrap = new Configuration();
$configBootstrap->bootstrap();
