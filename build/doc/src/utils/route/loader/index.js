import Structure from "@/data/structure/index";
import {normalize} from "@/utils/route/normalizer";

export const Routes = getItems(Structure());

function getItems(iterator) {
    const result = [];

    const register = function(item) {
        const route = makeRoute(item);
        if (route) {
            result.push(route);
        }
    }

    iterator.forEach(item => {
        if (!item.code) {
            return;
        }

        if (item.isVirtualParent) {
            if (item.children && item.children.length) {
                item.children.forEach(register);
                delete item.children;
            }
        }

        register(item);
    });

    return result;
}

function makeRoute(item) {
    const route = normalize(item);
    return Object.keys(route).length ? route : null;
}