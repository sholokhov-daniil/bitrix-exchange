<template>
  <EntitySelector
      v-model="model"
      :selector="selector"
      v-on="emitsAll"
  />
</template>

<script setup>
import {defineProps, computed, useAttrs} from 'vue';
import {EntitySelector} from "ui";

const model = defineModel();

const props = defineProps({
  entityId: {type: String, required: true},
  options: {type: Object, default: () => ({})}
});

const emitsAll = useAttrs();

const selector = computed(() => ({
  dialogOptions: {
    entities: [
      {
        id: 'sholokhov-exchange-uf',
        dynamicSearch: true,
        dynamicLoad: true,
        ...props.options
      }
    ]
  }
}))
</script>