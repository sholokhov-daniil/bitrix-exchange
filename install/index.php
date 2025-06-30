<?php

use Bitrix\Main\ORM\Fields;
use Bitrix\Main\Application;
use Bitrix\Main\ArgumentException;
use Bitrix\Main\DB\Connection;
use Bitrix\Main\DB\SqlQueryException;
use Bitrix\Main\EventManager;
use Bitrix\Main\Localization\Loc;

use Bitrix\Main\SystemException;

class sholokhov_exchange extends CModule
{
    var $MODULE_ID = "sholokhov.exchange";
    var $MODULE_NAME;
    var $MODULE_VERSION;
    var $MODULE_VERSION_DATE;
    var $MODULE_DESCRIPTION;

    private Connection $connection;

    private array $dropTables = [
        'sholokhov_exchange_settings',
        'sholokhov_exchange_result',
        'sholokhov_exchange_entities',
        'sholokhov_exchange_entity_type',
        'sholokhov_exchange_entity_settings',
        'sholokhov_exchange_entity_ui',
    ];

    public function __construct()
    {
        $arModuleVersion = [];

        include(__DIR__ . DIRECTORY_SEPARATOR . "version.php");
        if (is_array($arModuleVersion) && array_key_exists("VERSION", $arModuleVersion)) {
            $this->MODULE_VERSION = $arModuleVersion["VERSION"];
            $this->MODULE_VERSION_DATE = $arModuleVersion["VERSION_DATE"];
        } else {
            $this->MODULE_VERSION = $arModuleVersion['VERSION'];
            $this->MODULE_VERSION_DATE = $arModuleVersion['VERSION_DATE'];
        }

        $this->connection = Application::getConnection();

        $this->MODULE_NAME = Loc::getMessage("SHOLOKHOV_EXCHANGE_MODULE_NAME");
        $this->MODULE_DESCRIPTION = Loc::getMessage("SHOLOKHOV_EXCHANGE_MODULE_DESCRIPTION");
        $this->PARTNER_NAME = "Шолохов Даниил";
        $this->PARTNER_URI = "https://github.com/sholokhov-daniil";
    }

    /**
     * @return void
     * @throws ArgumentException
     * @throws SystemException
     */
    public function DoInstall(): void
    {
        $this->InstallDB();
        $this->InstallFiles();

        $this->registrationEvents();
        $this->Add();
        self::IncludeModule($this->MODULE_ID);
    }

    /**
     * @return void
     * @throws SqlQueryException
     */
    public function DoUninstall(): void
    {
        foreach ($this->dropTables as $table) {
            if ($this->connection->isTableExists($table)) {
                $this->connection->dropTable($table);
            }
        }

        $this->unRegistrationEvents();

        $this->UnInstallFiles();
        $this->Remove();
    }

    /**
     * @return void
     */
    public function InstallFiles(): void
    {
        CopyDirFiles(
            __DIR__ . DIRECTORY_SEPARATOR . 'files' . DIRECTORY_SEPARATOR . 'admin',
            $_SERVER['DOCUMENT_ROOT'] . DIRECTORY_SEPARATOR . 'bitrix' . DIRECTORY_SEPARATOR . 'admin',
            true,
            true
        );
    }

    /**
     * @return void
     */
    public function UnInstallFiles(): void
    {
        DeleteDirFiles(
            __DIR__ . DIRECTORY_SEPARATOR . 'files' . DIRECTORY_SEPARATOR . 'admin',
            $_SERVER['DOCUMENT_ROOT'] . DIRECTORY_SEPARATOR . 'bitrix' . DIRECTORY_SEPARATOR . 'admin'
        );
    }

    /**
     * @return void
     * @throws ArgumentException
     * @throws SystemException
     */
    public function InstallDB(): void
    {
        foreach ($this->dropTables as $table) {
            if ($this->connection->isTableExists($table)) {
                $this->connection->dropTable($table);
            }
        }

        $this->connection->createTable(
            'sholokhov_exchange_result',
            [
                'ID' => (new Fields\IntegerField('ID'))
            ],
            ['ID'],
            ['ID'],
        );

        $this->connection->createTable(
            'sholokhov_exchange_entity_type',
            [
                'CODE' => (new Fields\StringField('CODE'))->configurePrimary(),
            ]
        );

        $this->connection->createTable(
            'sholokhov_exchange_entities',
            [
                'CODE' => (new Fields\StringField('CODE')),
                'TYPE_CODE' => (new Fields\StringField('TYPE_CODE'))->configureRequired(),
                'ENTITY' => (new Fields\StringField('ENTITY'))->configureRequired(),
                'NAME' => (new Fields\StringField('NAME'))->configureRequired(),
                'DESCRIPTION' => (new Fields\StringField('DESCRIPTION'))->configureDefaultValue(''),
            ],
            ['CODE']
        );

        $this->connection->createTable(
            'sholokhov_exchange_settings',
            [
                'HASH' => (new Fields\StringField('HASH'))
                    ->configurePrimary(),

                'ACTIVE' => (new Fields\BooleanField('ACTIVE'))
                    ->configureRequired()
                    ->configureDefaultValue(true),

                'NAME' => (new Fields\StringField('NAME'))
                    ->configureSize(255)
                    ->configureDefaultValue(''),

                'DESCRIPTION' => (new Fields\StringField('DESCRIPTION'))
                    ->configureSize(255)
                    ->configureDefaultValue(''),

                'SETTINGS' => (new Fields\TextField('SETTINGS'))
                    ->configureDefaultValue(''),

                'SOURCE_SETTINGS_ID' => (new Fields\IntegerField('SOURCE_SETTINGS_ID'))
                    ->configureRequired(),

                'TARGET_SETTINGS_ID' => (new Fields\IntegerField('TARGET_SETTINGS_ID'))
                    ->configureRequired(),

                'DATE_CREATE' => (new Fields\DatetimeField('DATE_CREATE'))
                    ->configureRequired()
                    ->configureDefaultValueNow(),

                'DATE_UPDATE' => (new Fields\DatetimeField('DATE_UPDATE'))
                    ->configureRequired()
                    ->configureDefaultValueNow(),

                'USER_ID_CREATED' => (new Fields\IntegerField('USER_ID_CREATED'))
                    ->configureRequired(),

                'USER_ID_UPDATED' => (new Fields\IntegerField('USER_ID_UPDATED'))
                    ->configureRequired(),
            ],
            ['HASH']
        );

        $this->connection->createTable(
            'sholokhov_exchange_entity_ui',
            [
                'ID' => (new Fields\IntegerField('ID')),

                'ENTITY_CODE' => (new Fields\StringField('ENTITY_CODE'))
                    ->configureRequired()
                    ->configureUnique(),

                'SETTINGS' => (new Fields\TextField('SETTINGS'))
                    ->configureRequired(),
            ],
            ['ID'],
            ['ID']
        );


        $this->migrationEntities();
    }

    /**
     * @return void
     * @throws ArgumentException
     * @throws SystemException
     * @throws Exception
     */
    private function migrationEntities(): void
    {
        $this->migrationEntitySources();
        $this->migrationEntityTargets();
        $this->migrationEntityUI();
    }

    /**
     * Миграция источников данных
     *
     * @return void
     * @throws Exception
     */
    private function migrationEntitySources(): void
    {
        $type = 'source';
        $this->connection->add('sholokhov_exchange_entity_type', [
            'CODE' => $type,
        ]);

        $this->connection->add(
            'sholokhov_exchange_entities',
            [
                "CODE" => 'source_simple_xml',
                "TYPE_CODE" => $type,
                "ENTITY" => 'Sholokhov\\Exchange\\Source\\SimpleXml',
                "NAME" => 'SHOLOKHOV_EXCHANGE_SOURCE_SIMPLE_XML_NAME',
                "DESCRIPTION" => 'SHOLOKHOV_EXCHANGE_SOURCE_SIMPLE_XML_DESC',
            ]
        );

        $this->connection->add(
            'sholokhov_exchange_entities',
            [
                "CODE" => 'source_db_xml',
                "TYPE_CODE" => $type,
                "ENTITY" => 'Sholokhov\\Exchange\\Source\\Xml',
                "NAME" => 'SHOLOKHOV_EXCHANGE_SOURCE_DB_XML_NAME',
                "DESCRIPTION" => 'SHOLOKHOV_EXCHANGE_SOURCE_DB_XML_DESC',
            ]
        );

        $this->connection->add(
            'sholokhov_exchange_entities',
            [
                "CODE" => 'source_simple_csv',
                "TYPE_CODE" => $type,
                "ENTITY" => 'Sholokhov\\Exchange\\Source\\Csv',
                "NAME" => 'SHOLOKHOV_EXCHANGE_SOURCE_SIMPLE_CSV_NAME',
                "DESCRIPTION" => 'SHOLOKHOV_EXCHANGE_SOURCE_SIMPLE_CSV_DESC',
            ]
        );
        $this->connection->add(
            'sholokhov_exchange_entities',
            [
                "CODE" => 'source_simple_json_file',
                "TYPE_CODE" => $type,
                "ENTITY" => 'Sholokhov\\Exchange\\Source\\JsonFile',
                "NAME" => 'SHOLOKHOV_EXCHANGE_SOURCE_SIMPLE_JSON_NAME',
                "DESCRIPTION" => 'SHOLOKHOV_EXCHANGE_SOURCE_SIMPLE_JSON_DESC',
            ]
        );

        $this->connection->add(
            'sholokhov_exchange_entities',
            [
                "CODE" => 'source_iblock_element',
                "TYPE_CODE" => $type,
                "ENTITY" => 'Sholokhov\\Exchange\\Source\\Entities\\IBlock\\Element',
                "NAME" => 'SHOLOKHOV_EXCHANGE_SOURCE_ELEMENT_IBLOCK_NAME',
            ]
        );
    }

    /**
     * @return void
     * @throws Exception
     */
    private function migrationEntityTargets(): void
    {
        $type = 'target';
        $this->connection->add('sholokhov_exchange_entity_type', ['CODE' => $type]);

        $this->connection->add(
            'sholokhov_exchange_entities',
            [
                "CODE" => 'target_file',
                "TYPE_CODE" => $type,
                "ENTITY" => 'Sholokhov\\Exchange\\Target\\File',
                "NAME" => 'SHOLOKHOV_EXCHANGE_TARGET_FILE_NAME',
                "DESCRIPTION" => 'SHOLOKHOV_EXCHANGE_TARGET_FILE_DESC',
            ]
        );

        $this->connection->add(
            'sholokhov_exchange_entities',
            [
                "CODE" => 'target_hl_element',
                "TYPE_CODE" => $type,
                "ENTITY" => 'Sholokhov\\Exchange\\Target\\Highloadblock\\Element',
                "NAME" => 'SHOLOKHOV_EXCHANGE_TARGET_HL_ELEMENT_NAME',
            ]
        );

        $this->connection->add(
            'sholokhov_exchange_entities',
            [
                "CODE" => 'target_iblock_element_simple_product',
                "TYPE_CODE" => $type,
                "ENTITY" => 'Sholokhov\\Exchange\\Target\\IBlock\\Catalog\\SimpleProduct',
                "NAME" => 'SHOLOKHOV_EXCHANGE_TARGET_IBLOCK_ELEMENT_SIMPLE_PRODUCT_NAME',
            ]
        );

        $this->connection->add(
            'sholokhov_exchange_entities',
            [
                "CODE" => 'target_iblock_property_enum_value',
                "TYPE_CODE" => $type,
                "ENTITY" => 'Sholokhov\\Exchange\\Target\\IBlock\\Property\\PropertyEnumeration',
                "NAME" => 'SHOLOKHOV_EXCHANGE_TARGET_IBLOCK_PROPERTY_ENUM_VALUE_NAME',
            ]
        );

        $this->connection->add(
            'sholokhov_exchange_entities',
            [
                "CODE" => 'target_iblock_element',
                "TYPE_CODE" => $type,
                "ENTITY" => 'Sholokhov\\Exchange\\Target\\IBlock\\Element',
                "NAME" => 'SHOLOKHOV_EXCHANGE_TARGET_IBLOCK_ELEMENT_NAME',
            ]
        );

        $this->connection->add(
            'sholokhov_exchange_entities',
            [
                "CODE" => 'target_iblock_section',
                "TYPE_CODE" => $type,
                "ENTITY" => 'Sholokhov\\Exchange\\Target\\IBlock\\Section',
                "NAME" => 'SHOLOKHOV_EXCHANGE_TARGET_IBLOCK_SECTION_NAME',
            ]
        );

        $this->connection->add(
            'sholokhov_exchange_entities',
            [
                "CODE" => 'target_uf_enum_value',
                "TYPE_CODE" => $type,
                "ENTITY" => 'Sholokhov\\Exchange\\Target\\UserFields\\Enumeration',
                "NAME" => 'SHOLOKHOV_EXCHANGE_TARGET_UF_ENUM_VALUE_NAME',
            ]
        );
    }

    private function migrationEntityUI(): void
    {
        $this->connection->add(
            'sholokhov_exchange_entity_ui',
            [
                'ENTITY_CODE' => 'target_iblock_element',
                'SETTINGS' => json_encode([
                    [
                        'view' => 'checkbox',
                        'options' => [
                            'title' => 'SHOLOKHOV_EXCHANGE_SETTINGS_ENTITY_UI_TARGET_TITLE_FIELD_DEACTIVATE',
                            'attributes' => [
                                'name' => 'target[deactivate]',
                            ]
                        ]
                    ],
                    [
                        'view' => 'entity-selector',
                        'options' => [
                            'title' => 'SHOLOKHOV_EXCHANGE_SETTINGS_UI_TITLE_RENDER_IBLOCK_SELECT_IBLOCK',
                            'selector' => [
                                'multiple' => false,
                                'addButtonCaption' => 'SHOLOHKOV_EXCHANGE_UI_ENTITY_SELECTOR_DIALOG_ADD_BUTTON_CAPTION_SELECT',
                                'dialogOptions' => [
                                    'entities' => [
                                        [
                                            'id' => 'sholokhov-exchange-iblock',
                                            'dynamicSearch' => true,
                                            'dynamicLoad' => true,
                                        ]
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]),
            ]
        );

        $this->connection->add(
            'sholokhov_exchange_entity_ui',
            [
                'ENTITY_CODE' => 'target_iblock_section',
                'SETTINGS' => json_encode([
                    [
                        'view' => 'checkbox',
                        'options' => [
                            'title' => 'SHOLOKHOV_EXCHANGE_SETTINGS_ENTITY_UI_TARGET_TITLE_FIELD_DEACTIVATE',
                            'attributes' => [
                                'name' => 'target[deactivate]',
                            ]
                        ]
                    ],
                    [
                        'view' => 'entity-selector',
                        'options' => [
                            'title' => 'SHOLOKHOV_EXCHANGE_SETTINGS_UI_TITLE_RENDER_IBLOCK_SELECT_IBLOCK',
                            'selector' => [
                                'multiple' => false,
                                'addButtonCaption' => 'SHOLOHKOV_EXCHANGE_UI_ENTITY_SELECTOR_DIALOG_ADD_BUTTON_CAPTION_SELECT',
                                'dialogOptions' => [
                                    'entities' => [
                                        [
                                            'id' => 'sholokhov-exchange-iblock',
                                            'dynamicSearch' => true,
                                            'dynamicLoad' => true,
                                        ]
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]),
            ]
        );

        $this->connection->add(
            'sholokhov_exchange_entity_ui',
            [
                'ENTITY_CODE' => 'target_iblock_element_simple_product',
                'SETTINGS' => json_encode([
                    [
                        'view' => 'checkbox',
                        'options' => [
                            'title' => 'SHOLOKHOV_EXCHANGE_SETTINGS_ENTITY_UI_TARGET_TITLE_FIELD_DEACTIVATE',
                            'attributes' => [
                                'name' => 'target[deactivate]',
                            ]
                        ]
                    ],
                    [
                        'view' => 'entity-selector',
                        'options' => [
                            'title' => 'SHOLOKHOV_EXCHANGE_SETTINGS_UI_TITLE_RENDER_IBLOCK_SELECT_IBLOCK',
                            'selector' => [
                                'multiple' => false,
                                'addButtonCaption' => 'SHOLOHKOV_EXCHANGE_UI_ENTITY_SELECTOR_DIALOG_ADD_BUTTON_CAPTION_SELECT',
                                'dialogOptions' => [
                                    'entities' => [
                                        [
                                            'id' => 'sholokhov-exchange-iblock',
                                            'dynamicSearch' => true,
                                            'dynamicLoad' => true,
                                        ]
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]),
            ]
        );

        $this->connection->add(
            'sholokhov_exchange_entity_ui',
            [
                'ENTITY_CODE' => 'target_iblock_property_enum_value',
                'SETTINGS' => json_encode([
                    [
                        'view' => 'iblock-property',
                        'options' => [
                            'property' => [
                                'api' => [
                                    'propertyType' => 'L'
                                ]
                            ]
                        ]
                    ]
                ])
            ]
        );

        $this->connection->add(
            'sholokhov_exchange_entity_ui',
            [
                'ENTITY_CODE' => 'target_uf_enum_value',
                'SETTINGS' => json_encode([
                    [
                        'view' => 'uf-property',
                        'options' => [
                            'property' => [
                                'api' => [
                                    'propertyType' => 'enumeration'
                                ]
                            ]
                        ]
                    ]
                ])
            ]
        );

        $this->connection->add(
            'sholokhov_exchange_entity_ui',
            [
                'ENTITY_CODE' => 'target_hl_element',
                'SETTINGS' => json_encode([
                    [
                        'view' => 'entity-selector',
                        'options' => [
                            'title' => 'SHOLOKHOV_EXCHANGE_SETTINGS_UI_TITLE_RENDER_IBLOCK_SELECT_IBLOCK',
                            'selector' => [
                                'multiple' => false,
                                'addButtonCaption' => 'SHOLOHKOV_EXCHANGE_UI_ENTITY_SELECTOR_DIALOG_ADD_BUTTON_CAPTION_SELECT',
                                'dialogOptions' => [
                                    'entities' => [
                                        [
                                            'id' => 'sholokhov-exchange-highloadblock',
                                            'dynamicSearch' => true,
                                            'dynamicLoad' => true,
                                        ]
                                    ]
                                ]
                            ]
                        ]
                    ]
                ])
            ]
        );

        $this->connection->add(
            'sholokhov_exchange_entity_ui',
            [
                'ENTITY_CODE' => 'target_file',
                'SETTINGS' => json_encode([
                    [
                        'view' => 'input',
                        'options' => [
                            'title' => 'SHOLOKHOV_EXCHANGE_SETTINGS_ENTITY_UI_TARGET_FILE_TITLE_FIELD_MODULE_ID',
                            'attributes' => [
                                'name' => 'target[module_id]',
                            ],
                        ]
                    ]
                ])
            ]
        );
    }

    private function registrationEvents(): void
    {
        $eventManager = EventManager::getInstance();
        $eventManager->registerEventHandlerCompatible("main", "OnBeforeProlog", $this->MODULE_ID);
    }

    private function unRegistrationEvents(): void
    {
        $eventManager = EventManager::getInstance();
        $eventManager->unRegisterEventHandler("main", "OnBeforeProlog", $this->MODULE_ID);
    }
}
