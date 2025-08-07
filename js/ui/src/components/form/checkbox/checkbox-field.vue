<script setup>
import {defineProps,} from 'vue';

const model = defineModel({default: false});
const props = defineProps({
  modelValue: {type: Boolean, default: () => false },
  label: {type: String, default: () => '' }
});
</script>

<template>
  <label class="ui-ctl ui-ctl-checkbox">
    <input v-model="model" type="checkbox" class="ui-ctl-element">
    <div class="ui-ctl-label-text">{{ label }}</div>
  </label>
</template>