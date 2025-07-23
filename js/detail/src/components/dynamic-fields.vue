<script setup>
import {getMessage} from "utils";
import {defineModel, defineProps} from 'vue';
import {DynamicField, GridRow} from 'ui';

const model = defineModel({default: {}});
defineProps({
  fields: {type: Array, default: () => []}
})
</script>

<template>
  <template v-for="(field, key) in fields" :key="key">
    <DynamicField
        v-if="field.isConstructor"
        v-model="model[field.name]"
        :view="field.view"
        :entity="model.type"
        :options="field.options"
    />
    <GridRow v-else>
      <template #title>
        {{ getMessage(field?.title) }}
      </template>
      <template #content>
        {{ field }}

        <DynamicField
            v-model="model[field.name]"
            :view="field.view"
            :entity="model.type"
            :options="field.options"
        />
      </template>
    </GridRow>
  </template>
</template>