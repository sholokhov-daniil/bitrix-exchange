export default (item) => {
    if (!item.code) {
        return {};
    }

    let menuItem = {
        path: pathMap[item.code] || '',
        children: item?.children || [],

    };

    if (Array.isArray(menuItem.children) && menuItem.children.length) {
        menuItem.children.push({
            code: item.code,
            path: '',
            meta: item?.meta || {},
        });
    } else {
        menuItem.name = item.code;
        menuItem.meta = item?.meta || {};
        menuItem.component = componentMap[item.code] || null;
    }

    return menuItem;
}

const componentMap = {
    'home': () => import("@/pages/HomePage.vue"),
    'started': () => import("@/pages/StartedPage.vue"),
    'events': () => import("@/pages/events/EventPage.vue"),
    'events-highload': () => import("@/pages/SectionPage.vue"),
    'events-highload-import-element': () => import("@/pages/events/highload/ElementPage.vue"),
    'events-iblock': () => import("@/pages/SectionPage.vue"),
    'events-iblock-element': () => import("@/pages/events/iblock/ElementPage.vue"),
    'events-iblock-property': () => import("@/pages/SectionPage.vue"),
    'events-iblock-property-list-enumeration': () => import("@/pages/events/iblock/property/ListEnumiration.vue"),
};

const pathMap = {
    'home': '/',
    'started': '/started',
    'events': '/events',
    'events-highload': 'highload',
    'events-highload-import-element': 'element',
    'events-iblock': 'iblock',
    'events-iblock-element': 'element',
    'events-iblock-property': 'property',
    'events-iblock-property-list-enumeration': 'list-enumeration',
}


