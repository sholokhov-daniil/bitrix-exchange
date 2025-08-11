<template>
  <GridRow>
    <template #title>Свойство в которое записывается значение</template>
    <template #content>
      <MapToField
          v-model="model.to"
          :target="target"
          :type="model.type" />
    </template>
  </GridRow>

  <GridRow>
    <template #title>Откуда брать значение</template>
    <template #content>
      <Input v-model="model.from" />
    </template>
  </GridRow>

  <GridRow>
    <template #title>Ключевое значение</template>
    <template #content>
      <CheckBox v-model="model.pripary" />
    </template>
  </GridRow>

  <GridRow>
    <template #title>Хранит хеш импорта</template>
    <template #content>
      <CheckBox v-model="model.hash" />
    </template>
  </GridRow>

  <GridRow>
    <template #title>Создавать значение при его отсутствии</template>
    <template #content>
      <CheckBox v-model="model.isCreatedLink" />
    </template>
  </GridRow>
</template>

<script setup>
import {defineModel, defineProps} from 'vue';
import MapToField from "@/components/fields/entity-selector/map-to-field.vue";
import {GridRow, Input, CheckBox} from "ui";

const model = defineModel({});
defineProps({
  target: {type: Object, required: true},
});
</script>