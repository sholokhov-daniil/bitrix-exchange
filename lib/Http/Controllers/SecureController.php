<?php

namespace Sholokhov\Exchange\Http\Controllers;

use Bitrix\Main\Engine\Controller;
use Ramsey\Uuid\Uuid;
use Sholokhov\Exchange\Http\Middleware\ModuleRightMiddleware;

/**
 * @internal
 * @since 1.2.0
 * @version 1.2.0
 */
final class SecureController extends Controller
{
    /**
     * Конфигурация обработчиков запроса
     *
     * @return array[]
     *
     * @since 1.2.0
     * @version 1.2.0
     */
    public function configureActions(): array
    {
        return [
            'generateHash' => [
                '+prefilters' => [
                    new ModuleRightMiddleware
                ],
            ]
        ];
    }

    /**
     * Генерация уникального хеша
     *
     * @return string
     *
     * @since 1.2.0
     * @version 1.2.0
     */
    public function generateHash(): string
    {
        return Uuid::uuid4()->toString();
    }
}