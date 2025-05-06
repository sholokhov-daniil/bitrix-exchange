export default () => [
    {
        title: 'Главная страница',
        code: 'home',
    },
    {
        title: 'Документация кода',
        link: './api/index.html',
    },
    {
        title: 'Использование',
        code: 'started',
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