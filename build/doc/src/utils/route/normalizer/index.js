export default (item) => {
    let menuItem = {
        path: pathMap[item?.code] || '',
        children: item?.children || [],
    };

    if (Array.isArray(menuItem.children) && menuItem.children.length) {
        menuItem.children.push({
            code: item?.code,
            path: '',
            meta: item?.meta || {},
        });
    } else {
        menuItem.name = item?.code || '';
        menuItem.meta = item?.meta || {};
        menuItem.component = componentMap[item.code] || null;
    }

    return menuItem;
}

const componentMap = {
    'installation': () => import("@/pages/started/InstalledPage.vue"),
    'created': () => import("@/pages/started/CreatedPage.vue"),
    'configuration': () => import("@/pages/started/ConfigurationPage.vue"),
    'source': () => import('@/pages/SectionPage.vue'),
    'map': () => import('@/pages/SectionPage.vue'),
    'map-base': () => import('@/pages/started/map/BasePage.vue'),
    'import': () => import('@/pages/SectionPage.vue'),
    'events': () => import("@/pages/events/EventPage.vue"),
    'events-highload': () => import("@/pages/SectionPage.vue"),
    'events-highload-import-element': () => import("@/pages/events/highload/ElementPage.vue"),
    'events-iblock': () => import("@/pages/SectionPage.vue"),
    'events-iblock-element': () => import("@/pages/events/iblock/ElementPage.vue"),
    'events-iblock-property': () => import("@/pages/SectionPage.vue"),
    'events-iblock-property-list-enumeration': () => import("@/pages/events/iblock/property/ListEnumiration.vue"),
};

const pathMap = {
    'installation': '/installation/:hash?',
    'created': '/created/:hash?',
    'configuration': '/configuration/:hash?',
    'source': '/source',
    'map': '/map',
    'map-iblock-property': 'iblock-property',
    'map-base': 'base/:hash?',
    'import': '/target',
    'events': '/events',
    'events-highload': 'highload',
    'events-highload-import-element': 'element',
    'events-iblock': 'iblock',
    'events-iblock-element': 'element/:hash?',
    'events-iblock-property': 'property',
    'events-iblock-property-list-enumeration': 'list-enumeration/:hash?',
}


