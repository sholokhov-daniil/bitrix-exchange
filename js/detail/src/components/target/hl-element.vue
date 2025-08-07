<template>
  <EntitySelector
      v-model="model"
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

<script setup>
import {defineModel} from 'vue';
import {EntitySelector} from "ui";

const model = defineModel();
</script>