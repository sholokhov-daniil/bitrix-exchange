import { defineAsyncComponent } from 'vue';

export const internalView = (template) => {
    let component = null;

    switch (template) {
        case 'source_db_xml':
            component = defineAsyncComponent(() => import('@/components/source/bd-xml.vue'));
            break;
        case 'source_iblock_element':
            component = defineAsyncComponent(() => import('@/components/source/iblock-element.vue'));
            break;
        case 'source_simple_csv':
            component = defineAsyncComponent(() => import('@/components/source/simple-csv.vue'));
            break;
        case 'source_simple_json_file':
            component = defineAsyncComponent(() => import('@/components/source/simple-json-file.vue'));
            break;
        case 'source_simple_xml':
            component = defineAsyncComponent(() => import('@/components/source/simple-xml.vue'));
            break;
        case 'target_hl_element':
            component = defineAsyncComponent(() => import('@/components/target/hl-element.vue'));
            break;
        case 'target_iblock_element':
            component = defineAsyncComponent(() => import('@/components/target/iblock-element.vue'));
            break;
        case 'target_iblock_element_simple_product':
            component = defineAsyncComponent(() => import('@/components/target/iblock-simple-product.vue'))
            break;
        case 'target_iblock_property_enum_value':
            component = defineAsyncComponent(() => import('@/components/target/iblock-property-enum.vue'));
            break;
        case 'target_iblock_section':
            component = defineAsyncComponent(() => import('@/components/target/iblock-section.vue'));
            break;
        case 'target_uf_enum_value':
            component = defineAsyncComponent(() => import('@/components/target/uf-enum-value.vue'));
            break;
    }

    return component;
}