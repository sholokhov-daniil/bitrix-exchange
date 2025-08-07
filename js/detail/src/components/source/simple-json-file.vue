<template>
  <GridRow>
    <template #title>
      {{ getMessage('SHOLOKHOV_EXCHANGE_DETAIL_ENTITY_UI_FILE_PATH') }}
    </template>
    <template #content>
      <Input v-model="model.path" />
    </template>
  </GridRow>

  <GridRow>
    <template #title>
      {{ getMessage('SHOLOKHOV_EXCHANGE_DETAIL_ENTITY_UI_SOURCE_JSON_SOURCE_KEY') }}
    </template>
    <template #content>
      <Input v-model="model.sourceKey" />
    </template>
  </GridRow>

  <GridRow>
    <template #title>
      {{ getMessage('SHOLOKHOV_EXCHANGE_DETAIL_ENTITY_UI_FILE_PATH') }}
    </template>
    <template #content>
      <check-box v-model="model.multiple" />
    </template>
  </GridRow>
</template>

<script setup>
import {defineModel} from 'vue';
import {GridRow, Input, CheckBox} from "ui";
import {getMessage} from "utils";

const model = defineModel();
</script>