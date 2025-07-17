<script setup>
import {computed, reactive, watch, defineModel} from 'vue';
import {getMessage, runAction} from "utils";
import {Select as SelectField, DynamicField} from 'ui';
import GridRow from "@/components/grid-row.vue";

const model = defineModel();
const data = reactive({
  targets: [],
  fields: [],
});

const fieldApi = computed(() => ({
  action: 'sholokhov:exchange.EntityController.getByType',
  data: {code: 'target'},
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
  <grid-row>
    <template #title>{{ getMessage('SHOLOKHOV_EXCHANGE_SETTINGS_ENTITY_UI_TARGET_TITLE_FIELD_TYPE') }}</template>
    <template #content>
      <SelectField v-model="model.type" :api="fieldApi" name="type" />
    </template>
  </grid-row>

  <grid-row v-for="(field, key) in data.fields" :key="key">
    <template #title>
      {{ getMessage(field?.title) }}
    </template>
    <template #content>
      <dynamic-field
          v-model="model[field.name]"
          :view="field.view"
          :entity="model.type"
          :options="field.options"
      />
    </template>
  </grid-row>
</template>