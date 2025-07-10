<?php

use Sholokhov\Exchange\Helper\Helper;

use Bitrix\Main\Event;
use Bitrix\Main\EventResult;
use Bitrix\Main\Localization\Loc;

/**
 * @since 1.2.0
 * @version 1.2.0
 */
class SholokhovExchangeSettingDetails extends \CBitrixComponent
{
    /**
     * @param $arParams
     * @return array
     *
     * @since 1.2.0
     * @version 1.2.0
     */
    public function onPrepareComponentParams($arParams): array
    {
        $arParams['ID'] = (int)$arParams['ID'];

        return $arParams;
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

        $tabs = $this->getDefaultTabs();
        $this->loadCustomTabs();

        $this->arResult['CONTROL'] = new CAdminTabControl('se_detail_control', $tabs);
        $this->arResult['JS_DATA'] = $this->getJsData($this->arResult['CONTROL']);

        $this->includeComponentTemplate();
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
                'container' => []
            ],
            'DATA' => [
                'id' => $this->arParams['ID']
            ]
        ];

        foreach ($control->tabs as $tab) {
            $result['OPTIONS']['container'][$tab['CONTAINER']] = '#' . $tab['DIV'];
        }

        return $result;
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
     * @return void
     *
     * @since 1.2.0
     * @version 1.2.0
     */
    private function loadCustomTabs(): void
    {
        $this->arResult['CUSTOM_TABS'] = [];

        $eventParameters = [
            'id' => $this->arParams['ID']
        ];

        $event = new Event(Helper::getModuleID(), 'onAdminDetailTabs', $eventParameters);
        $event->send();

        foreach($event->getResults() as $result) {
            if ($result->getType() === EventResult::SUCCESS) {
                $customTabs = $result->getParameters();

                if (is_array($customTabs) && is_callable($customTabs['RENDER'])) {
                    $this->arResult['CUSTOM_TABS'] = array_merge($this->arResult['CUSTOM_TABS'], $customTabs);
                }
            }
        }
    }
}