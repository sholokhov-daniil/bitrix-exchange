<script setup>
import {defineModel} from 'vue';
import {InputHash, CheckBox, GridRow} from 'ui';
import {getMessage} from "utils";

const model = defineModel();
</script>

<template>
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