<?php

namespace Sholokhov\Exchange\Http\Controllers;

use Sholokhov\Exchange\Helper\IBlock;
use Sholokhov\Exchange\Http\Middleware\ModuleLoaderMiddleware;

use Bitrix\Main\ArgumentException;
use Bitrix\Main\Engine\Controller;
use Bitrix\Main\LoaderException;
use Bitrix\Main\ObjectPropertyException;
use Bitrix\Main\SystemException;

/**
 * @internal
 * @since 1.2.0
 * @version 1.2.0
 */
final class IBlockController extends Controller
{
    /**
     * @return array[]
     *
     * @since 1.2.0
     * @version 1.2.0
     */
    public function configureActions(): array
    {
        return [
            'getTypes' => [
                '+prefilters' => [
                    new ModuleLoaderMiddleware('iblock'),
                ]
            ],
            'getIBlocks' => [
                '+prefilters' => [
                    new ModuleLoaderMiddleware('iblock'),
                ]
            ]
        ];
    }

    /**
     * @param array{language: string, order: array, filter: array} $parameters
     * @return array
     * @throws ArgumentException
     * @throws ObjectPropertyException
     * @throws SystemException
     * @throws LoaderException
     * @throws \DateInvalidOperationException
     *
     * @since 1.2.0
     * @version 1.2.0
     */
    public function getTypesAction(array $parameters = []): array
    {
        $iterator = IBlock::getAvailableTypes(
            [
                'select' => [
                    'ID',
                    'NAME' => 'LANG_MESSAGE.NAME'
                ]
            ]
        );

        return array_map(
            fn($type) => [
                'id' => $type['ID'],
                'name' => $type['NAME']
            ],
            $iterator
        );
    }

    /**
     * Получить доступные инфоблоки
     *
     * @param array $parameters
     * @return array
     * @throws ArgumentException
     * @throws LoaderException
     * @throws ObjectPropertyException
     * @throws SystemException
     * @throws \DateInvalidOperationException
     *
     * @since 1.2.0
     * @version 1.20
     */
    public function getIBlocksAction(array $parameters = []): array
    {
        $iterator = IBlock::getAvailableIBlock($parameters);

        return array_map(
            fn($iBlock) => [
                'id' => $iBlock['ID'],
                'name' => $iBlock['NAME']
            ],
            $iterator
        );
    }
}