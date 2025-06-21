<?php

namespace Sholokhov\Exchange\Http\Controllers\UI;

use Bitrix\Iblock\TypeTable;
use Bitrix\Main\Engine\Controller;
use Bitrix\Main\Engine\Response\HtmlContent;
use Sholokhov\Exchange\UI\HtmlContentArea;
use Sholokhov\Exchange\Http\Middleware\ModuleRightMiddleware;

/**
 * @internal
 * @since 1.2.0
 * @version 1.2.0
 */
final class IBlockController extends Controller
{
    /**
     * Конфигурация контроллера
     *
     * @return array[]
     *
     * @since 1.2.0
     * @version 1.2.0
     */
    public function configureActions(): array
    {
        return [
            'getIBlockSelector' => [
                '+prefilters' => [
                    new ModuleRightMiddleware
                ],
            ]
        ];
    }

    public function getIBlockSelectorAction(): HtmlContent
    {
        TypeTable::getList();

        $html = '<select>';

        
        return new HtmlContent(new HtmlContentArea($html));
    }
}