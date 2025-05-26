export default () => [
    {
        title: 'Начало работы',
        children: [
            {
                title: 'Установка',
                code: 'installation',
            },
            {
                title: 'Конфигурация',
                code: 'configuration',
            },
            {
                title: 'Карта обмена',
                code: 'map',
                children: [
                    {
                        title: 'Основное свойство',
                        code: 'map-base',
                    },
                    {
                        title: 'Информационный блок',
                        code: 'map-iblock-property',
                    }
                ],
            },
            {
                title: 'Создание обмена',
                code: 'created',
            },
        ]
    },
    {
        title: 'Документация кода',
        link: './api/index.html',
    },
    {
        title: 'Источник данных',
        code: 'source',
        meta: {
            seo: {
                h1: 'Источники данных'
            }
        }
    },
    {
      title: 'Импорт',
      code: 'import',
        children: [
            {
                title: 'Файл',
                code: 'import-file',
            },
            {
                title: 'Пользовательские свойства',
                code: 'import-uf',
                meta: {
                    seo: {
                        h1: 'Импорт пользовательских свойств UF'
                    }
                },
                children: [
                    {
                        title: 'Значение списка',
                        code: 'import-uf-enumeration-value',
                    }
                ]
            }
        ]
    },
    {
        title: 'События',
        code: 'events',
        children: [
            {
                title: 'Информационный блок',
                code: 'events-iblock',
                meta: {
                    seo: {
                        h1: 'События информационного блока'
                    }
                },
                children: [
                    {
                        title: 'Свойства',
                        code: 'events-iblock-property',
                        meta: {
                            seo: {
                                h1: 'События свойств информационного блока',
                            }
                        },
                        children: [
                            {
                                title: 'Импорт значений списка',
                                code: 'events-iblock-property-list-enumeration',
                            }
                        ],
                    },
                    {
                        title: 'Элемент',
                        code: 'events-iblock-element',
                    },
                    {
                        title: 'Раздел',
                        // code: 'events-iblock-section',
                    }
                ]
            },
            {
                title: 'Справочник (highload)',
                code: 'events-highload',
                meta: {
                    seo: {
                        h1: 'События справочника (highload-блок)'
                    }
                },
                children: [
                    {
                        title: 'Элемент',
                        code: 'events-highload-import-element'
                    }
                ]
            }
        ]
    }
];