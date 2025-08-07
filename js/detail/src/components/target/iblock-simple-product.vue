<template>
  <GridRow>
    <template #title>
      {{ getMessage('SHOLOKHOV_EXCHANGE_DETAIL_ENTITY_UI_TARGET_TITLE_FIELD_DEACTIVATE') }}
    </template>
    <template #content>
      <CheckBox v-model="model.deactivate" name="deactivate" />
    </template>
  </GridRow>

  <GridRow>
    <template #title>
      {{ getMessage('SHOLOKHOV_EXCHANGE_DETAIL_UI_TITLE_RENDER_IBLOCK_SELECT_IBLOCK') }}
    </template>
    <template #content>
      <EntitySelector
          v-model="model.entityId"
          :options="options"
      />
    </template>
  </GridRow>
</template>

<script setup>
import {defineModel} from 'vue';
import {GridRow, CheckBox, EntitySelector} from "ui";
import {getMessage} from "utils";

const model = defineModel();

const options = {
  multiple: false,
  addButtonCaption: 'SHOLOKHOV_EXCHANGE_DETAIL_UI_TITLE_RENDER_IBLOCK_SELECT_IBLOCK',
  dialogOptions: {
    entities: [
      {
        id: 'sholokhov-exchange-iblock',
        dynamicSearch: true,
        dynamicLoad: true
      }
    ]
  }
};
</script>