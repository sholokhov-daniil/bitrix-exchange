<script setup>
import {defineModel, ref} from 'vue';
import {InputHash, CheckBox, GridRow} from 'ui';
import {getMessage} from "utils";

const containerRef = ref();
const model = defineModel();
</script>

<template>
  <GridRow>
    <template #content>
      <div ref="containerRef"></div>
    </template>
  </GridRow>

  <GridRow>
    <template #title>
      {{ getMessage('SHOLOKHOV_EXCHANGE_DETAIL_ENTITY_UI_GENERAL_TITLE_FIELD_ACTIVE') }}
    </template>
    <template #content>
      <CheckBox v-model="model.active" />
    </template>
  </GridRow>

  <GridRow>
    <template #title>
      {{ getMessage('SHOLOKHOV_EXCHANGE_DETAIL_ENTITY_UI_GENERAL_TITLE_FIELD_HASH') }}
    </template>
    <template #content>
      <InputHash v-model="model.hash" />
    </template>
  </GridRow>
</template>