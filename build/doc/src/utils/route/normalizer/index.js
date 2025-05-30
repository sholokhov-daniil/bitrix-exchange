export const normalize = (item) => {
    let route = {
        path: pathMap[item?.code] || '',
        name: item?.code || '',
        meta: item?.meta || {},
        component: componentMap[item.code] || null,
    };

    if (item.redirect) {
        route.redirect = item.redirect;
    }

    if (Array.isArray(item.children) && item.children.length) {
        route.children = item.children.map(normalize);
    }

    return route;
}

const componentMap = {
    'started': () => import('@/pages/HomePage.vue'),
    'installation': () => import("@/pages/started/InstalledPage.vue"),
    'created': () => import("@/pages/started/CreatedPage.vue"),
    'configuration': () => import("@/pages/started/ConfigurationPage.vue"),
    'source': () => import('@/pages/SectionPage.vue'),
    'map-base': () => import('@/pages/started/map/BasePage.vue'),
    'map-iblock-property': () => import('@/pages/started/map/iblock/IBlockPage.vue'),
    'import-file': () => import('@/pages/target/import/FilePage.vue'),
    'import-iblock-element': () => import('@/pages/target/import/iblock/ElementPage.vue'),
    'import-iblock-section': () => import('@/pages/target/import/iblock/SectionPage.vue'),
    'import-hl-element': () => import('@/pages/target/import/hl/ElementPage.vue'),
    'import-iblock-enumeration-value': () => import('@/pages/target/import/iblock/property/enumiration/ValuePage.vue'),
    'import-uf-enumeration-value': () => import('@/pages/target/import/uf/enumeration/ValuePage.vue'),
};

const pathMap = {
    'started': '/',
    'installation': '/installation/:hash?',
    'created': '/created/:hash?',
    'configuration': '/configuration/:hash?',
    'source': '/source',
    'map': '/map',
    'map-iblock-property': 'iblock-property',
    'map-base': 'base/:hash?',
    'import': '/target',
    'import-file': 'file/:hash?',
    'import-iblock-property': 'properties',
    'import-iblock-enumeration-value': 'enum-value/:hash?',
    'import-iblock-element': 'iblock-element/:hash?',
    'import-iblock-section': 'iblock-section/:hash?',
    'import-hl-element': 'hl-element/:hash?',
    'import-uf': 'uf',
    'import-uf-enumeration-value': 'enum-value/:hash?',
}


