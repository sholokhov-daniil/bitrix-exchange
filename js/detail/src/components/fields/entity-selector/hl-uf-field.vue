<template>
  <EntitySelector
      v-model="model"
      :entityId="entityTypeId"
      :options="options"
  />
</template>

<script setup>
import {defineProps, computed, defineModel} from 'vue';
import EntitySelector from './uf-field.vue';

const props = defineProps({
  entityId: {type: String, required: true},
  options: {type: Object, default: () => ({})}
});

const model = defineModel();

const entityTypeId = computed(() => `HLBLOCK_${props.entityId}`)
</script>