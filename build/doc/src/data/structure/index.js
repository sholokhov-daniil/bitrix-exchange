export default () => [
    {
        title: 'Начало работы',
        code: 'started',
        redirect: {
            name: 'installation'
        },
        isVirtualParent: true,
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
        title: 'Источник данных',
        code: 'source',
        children: [
            {
                title: 'Быстрый XML',
                code: 'source-simple-xml',
            },
            {
                title: 'Медленный XML',
                code: 'source-xml',
            },
            {
                title: 'Json',
                code: 'source-json'
            },
            {
                title: 'Json файл',
                code: 'source-json-file'
            }
        ],
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
                title: 'Элемент инфоблока',
                code: 'import-iblock-element',
            },
            {
                title: 'Раздел инфоблока',
                code: 'import-iblock-section',
            },
            {
                title: 'Элемент справочника',
                code: 'import-hl-element',
            },
            {
                title: 'Свойства информационного блока',
                code: 'import-iblock-property',
                children: [
                    {
                        title: 'Значение списка',
                        code: 'import-iblock-enumeration-value'
                    }
                ]
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
        title: 'Документация кода',
        link: './api/index.html',
    },
];