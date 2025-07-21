<?php

use Sholokhov\Exchange\Helper\Helper;

use Sholokhov\Exchange\UI\DTO\UIFieldInterface;

use Bitrix\Main\Event;
use Bitrix\Main\EventResult;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Error;
use Bitrix\Main\Engine\Contract\Controllerable;
use Bitrix\Main\Errorable;
use Bitrix\Main\ErrorCollection;

/**
 * @since 1.2.0
 * @version 1.2.0
 */
class SholokhovExchangeSettingDetails extends CBitrixComponent implements Errorable, Controllerable
{
    /**
     * @var ErrorCollection
     *
     * @since 1.2.0
     * @version 1.2.0
     */
    private ErrorCollection $errorCollection;

    /**
     * @param $arParams
     * @return array
     *
     * @since 1.2.0
     * @version 1.2.0
     */
    public function onPrepareComponentParams($arParams): array
    {
        $this->errorCollection = new ErrorCollection;
        $arParams['ID'] = (int)$arParams['ID'];

        return $arParams;
    }

    /**
     * @return array[]
     *
     * @since 1.2.0
     * @version 1.2.0
     */
    public function configureActions(): array
    {
        return [
            'saveAction' => [
                '+prefilters' => [
                    // TODO: Добавить проверку прав доступа
                ]
            ]
        ];
    }

    /**
     * @return string[]
     *
     * @since 1.2.0
     * @version 1.2.0
     */
    public function listKeysSignedParameters(): array
    {
        return ['ID'];
    }

    public function saveAction(array $fields): array
    {
        $result = [];

        return $result;
    }

    /**
     * @return void
     *
     * @since 1.2.0
     * @version 1.2.0
     */
    public function executeComponent(): void
    {
        if (!$GLOBALS['USER']->IsAdmin()) {
            ShowMessage(Loc::getMessage('SHOLOKHOV_EXCHANGE_C_SETTINGS_DETAIL_ACCESS'));
            return;
        }

        $this->arResult['CONTROL'] = new CAdminTabControl('se_detail_control', $this->getTabs());
        $this->arResult['JS_DATA'] = $this->getJsData($this->arResult['CONTROL']);

        $this->includeComponentTemplate();
    }

    /**
     * @param Error $error
     * @return void
     *
     * @since 1.2.0
     * @version 1.2.0
     */
    public function addError(Error $error): void
    {
        $this->errorCollection->setError($error);
    }

    /**
     * @return array|Error[]
     *
     * @since 1.2.0
     * @version 1.2.0
     */
    public function getErrors(): array
    {
        return $this->errorCollection->getValues();
    }

    /**
     * @param $code
     * @return Error|null
     *
     * @since 1.2.0
     * @version 1.2.0
     */
    public function getErrorByCode($code): ?Error
    {
        return $this->errorCollection->getErrorByCode($code);
    }

    /**
     * @param CAdminTabControl $control
     * @return array
     *
     * @since 1.2.0
     * @version 1.2.0
     */
    private function getJsData(CAdminTabControl $control): array
    {
        $result = [
            'OPTIONS' => [
                'signed' => $this->getSignedParameters(),
                'teleport' => [],
                'fields' => [],
            ],
            'DATA' => [
                'id' => $this->arParams['ID'],
            ]
        ];

        foreach ($control->tabs as $tab) {
            if ($tab['CONTAINER']) {
                $result['OPTIONS']['teleport'][$tab['CONTAINER']] = '#' . $tab['DIV'] . '_edit_table';
            }

            if (!empty($tab['FIELDS']) && is_array($tab['FIELDS'])) {
                foreach ($tab['FIELDS'] as $field) {
                    if ($field instanceof UIFieldInterface) {
                        $result['OPTIONS']['fields']['#' . $tab['DIV'] . '_edit_table'][] = $field->toArray();
                    }
                }
            }
        }

        return $result;
    }

    /**
     * Получение доступных табов
     *
     * @return array
     * @since 1.2.0
     * @version 1.2.0
     */
    private function getTabs(): array
    {
        return array_merge($this->getDefaultTabs(), $this->getUserTabs());
    }

    /**
     * Получение стандартных пунктов
     *
     * @return array[]
     *
     * @since 1.2.0
     * @version 1.2.0
     */
    private function getDefaultTabs(): array
    {
        $generalContainer = uniqid('sholokhov_exchange_detail_general_');
        $targetContainer = uniqid('sholokhov_exchange_detail_target_');
        $sourceController = uniqid('sholokhov_exchange_detail_source_');
        $mapController = uniqid('sholokhov_exchange_detail_map_');

        return [
            [
                'DIV' => $generalContainer,
                'CONTAINER' => 'general',
                'TAB' => Loc::getMessage('SHOLOKHOV_EXCHANGE_C_SETTINGS_DETAIL_TAB_GENERAL'),
                "TITLE" => Loc::getMessage('SHOLOKHOV_EXCHANGE_C_SETTINGS_DETAIL_TAB_GENERAL_TITLE'),
            ],
            [
                'DIV' => $targetContainer,
                'CONTAINER' => 'target',
                'TAB' => Loc::getMessage('SHOLOKHOV_EXCHANGE_C_SETTINGS_DETAIL_TAB_TARGET'),
                "TITLE" => Loc::getMessage('SHOLOKHOV_EXCHANGE_C_SETTINGS_DETAIL_TAB_TARGET_TITLE'),
            ],
            [
                'DIV' => $sourceController,
                'CONTAINER' => 'source',
                'TAB' => Loc::getMessage('SHOLOKHOV_EXCHANGE_C_SETTINGS_DETAIL_TAB_SOURCE'),
                "TITLE" => Loc::getMessage('SHOLOKHOV_EXCHANGE_C_SETTINGS_DETAIL_TAB_SOURCE_TITLE'),
            ],
            [
                'DIV' => $mapController,
                'CONTAINER' => 'map',
                'TAB' => Loc::getMessage('SHOLOKHOV_EXCHANGE_C_SETTINGS_DETAIL_TAB_MAP'),
                "TITLE" => Loc::getMessage('SHOLOKHOV_EXCHANGE_C_SETTINGS_DETAIL_TAB_MAP_TITLE'),
            ],
        ];
    }

    /**
     * Загрузка пользовательских пунктов
     *
     * @return array
     *
     * @since 1.2.0
     * @version 1.2.0
     */
    private function getUserTabs(): array
    {
        $result = [];

        $eventParameters = [
            'id' => $this->arParams['ID']
        ];

        $event = new Event(Helper::getModuleID(), 'onAdminDetailTabs', $eventParameters);
        $event->send();

        foreach($event->getResults() as $eventResult) {
            if ($eventResult->getType() === EventResult::SUCCESS) {
                $tabs = $eventResult->getParameters();

                if (is_array($tabs)) {
                    $result = array_merge($result, $tabs);
                }
            }
        }

        return $result;
    }
}
