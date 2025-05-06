import Structure from "@/data/structure/index";
import Normalize from "@/utils/route/normalizer";

export const Routes = getItems(Structure());

function getItems(iterator) {
    const result = [];

    for(let item of iterator) {
        let menuItem = Normalize(item);

        if (!Object.keys(menuItem).length) {
            continue;
        }

        if (Array.isArray(item.children)) {
            menuItem.children = getItems(menuItem.children) || [];
        }

        result.push(menuItem);
    }

    return result;
}