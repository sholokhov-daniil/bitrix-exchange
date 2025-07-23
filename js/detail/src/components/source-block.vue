<script setup>
import {computed, reactive, watch, defineModel} from 'vue';
import {getMessage, runAction} from "utils";
import {Select as SelectField, GridRow} from 'ui';
import DynamicFields from "@/components/dynamic-fields.vue";

const model = defineModel();
const data = reactive({
  targets: [],
  fields: [],
});

const fieldApi = computed(() => ({
  action: 'sholokhov:exchange.EntityController.getByType',
  data: {code: 'source'},
  callback: normalizeTypeResponse,
}));

watch(() => model.value.type, (newValue) => {
  model.value = {
    type: model.value.type
  };

  loadFields(newValue);
});

const normalizeTypeResponse = (response) => {
  if (Array.isArray(response.data)) {
    response.data = response.data.map(type => ({
      value: type.CODE,
      name: type.NAME,
    }));
  }

  return response;
}

const loadFields = (type) => {
  runAction('sholokhov:exchange.EntityController.getFields', {code: type})
      .then(response => data.fields = Array.isArray(response.data) ? response.data : [])
      .catch(response => console.error(response));
}
</script>

<template>
  <GridRow>
    <template #title>{{ getMessage('SHOLOKHOV_EXCHANGE_SETTINGS_ENTITY_UI_SOURCE_TITLE_FIELD_TYPE') }}</template>
    <template #content>
      <SelectField v-model="model.type" :api="fieldApi" name="type" />
    </template>
  </GridRow>

  <DynamicFields v-model="model" :fields="data.fields" />
</template>