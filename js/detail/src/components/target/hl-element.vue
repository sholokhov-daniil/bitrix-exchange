<template>
  <GridRow>
    <template #title>
      {{ getMessage('SHOLOKHOV_EXCHANGE_DETAIL_UI_TITLE_RENDER_HL_SELECT_IBLOCK') }}
    </template>
    <template #content>
      <EntitySelector
          v-model="model.entityId"
          :options="{
        multiple: false,
        addButtonCaption: 'SHOLOHKOV_EXCHANGE_UI_ENTITY_SELECTOR_DIALOG_ADD_BUTTON_CAPTION_SELECT',
        dialogOptions: {
          entities: [
              {
                id: 'sholokhov-exchange-highloadblock',
                dynamicSearch: true,
                dynamicLoad: true
              }
          ]
        }
      }"
      />
    </template>
  </GridRow>
</template>

<script setup>
import {defineModel} from 'vue';
import {EntitySelector, GridRow} from "ui";
import {getMessage} from "utils";

const model = defineModel();
</script>