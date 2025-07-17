<script setup>
import {defineModel} from 'vue';
import {InputHash, CheckBox} from 'ui';
import {getMessage} from "utils";

const model = defineModel();
</script>

<template>
  <tr>
    <td width="40%"></td>
    <td width="60%">
      <CheckBox
          v-model="model.active"
          :label="getMessage('SHOLOKHOV_EXCHANGE_SETTINGS_ENTITY_UI_GENERAL_TITLE_FIELD_ACTIVE')"
          name="active"
      />
    </td>
  </tr>

  <tr>
    <td width="40%">
      {{ getMessage('SHOLOKHOV_EXCHANGE_SETTINGS_ENTITY_UI_GENERAL_TITLE_FIELD_HASH') }}
    </td>
    <td width="60%">
      <InputHash v-model="model.hash" name="hash" />
    </td>
  </tr>
</template>