<script setup>
import {defineModel, onMounted, ref} from 'vue';
import {InputHash, CheckBox, GridRow} from 'ui';
import {getMessage} from "utils";
import {view} from "@/view";

const containerRef = ref();
const model = defineModel();

onMounted(() => {
  view('TEST_1', {
    container: containerRef,
    store: model
  });



})
</script>

<template>
  <GridRow>
    <template #content>
      <div ref="containerRef"></div>
    </template>
  </GridRow>

  <GridRow>
    <template #content>
      <CheckBox
          v-model="model.active"
          :label="getMessage('SHOLOKHOV_EXCHANGE_SETTINGS_ENTITY_UI_GENERAL_TITLE_FIELD_ACTIVE')"
          name="active"
      />
    </template>
  </GridRow>

  <GridRow>
    <template #title>
      {{ getMessage('SHOLOKHOV_EXCHANGE_SETTINGS_ENTITY_UI_GENERAL_TITLE_FIELD_HASH') }}
    </template>
    <template #content>
      <InputHash v-model="model.hash" name="hash" />
    </template>
  </GridRow>
</template>