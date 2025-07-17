<script setup>
import {reactive, computed, defineProps } from 'vue';
import EntitySelector from './entity-selector-field.vue';
import {getMessage} from 'utils';

const props = defineProps({
  modelValue: {type: Object, default: () => {}},
  property: {type: Object, default: () => {}},
});
const emit = defineEmits(['update:modelValue'])

const data = reactive({
  iBlockOptions: {
    multiple: false,
    addButtonCaption: 'SHOLOHKOV_EXCHANGE_UI_ENTITY_SELECTOR_DIALOG_ADD_BUTTON_CAPTION_SELECT',
    dialogOptions: {
      entities: [
        {
          id: 'sholokhov-exchange-iblock',
          dynamicSearch: true,
          dynamicLoad: true,
        }
      ]
    },
  },
});

const model = computed({
  get() {
    if (!props.modelValue) {
      emit('update:modelValue', {});
    }
    return props.modelValue;
  },
  set(newValue) { emit('update:modelValue', newValue); }
})

const iBlockId = computed({
  get() { return model.value?.iblock_id},
  set(newValue) {
    if (!newValue) {
      propertyId.value = "";
    }

    model.value.iblock_id = newValue;
  }
});

const propertyId = computed({
  get() { return model.value?.property_id; },
  set(newValue) { model.value.property_id = newValue; }
})

const propertyOptions = computed(() => ({
  multiple: false,
  addButtonCaption: 'SHOLOHKOV_EXCHANGE_UI_ENTITY_SELECTOR_DIALOG_ADD_BUTTON_CAPTION_SELECT',
  dialogOptions: {
    entities: [
      {
        id: 'sholokhov-exchange-iblock-property',
        dynamicSearch: true,
        dynamicLoad: true,
        options: {
          iblockId: iBlockId,
          nameTemplate: '#NAME# (#CODE#)',
          ...props.property?.api || {}
        }
      }
    ]
  }
}))
</script>

<template>
  <tr>
    <td width="40%">
      {{ getMessage('SHOLOKHOV_EXCHANGE_SETTINGS_UI_TITLE_RENDER_IBLOCK_SELECT_IBLOCK') }}
    </td>
    <td width="60%">
      <EntitySelector v-model="iBlockId" :selector="data.iBlockOptions"/>
    </td>
  </tr>
  <tr v-if="iBlockId">
    <td width="40%">
      {{ getMessage('SHOLOKHOV_EXCHANGE_UI_ENTITY_PROPERTY_SELECTOR') }}
    </td>
    <td width="60%">
      <EntitySelector v-model="propertyId" :selector="propertyOptions"/>
    </td>
  </tr>
</template>