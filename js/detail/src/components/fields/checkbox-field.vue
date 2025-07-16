<script setup>
import {defineProps, defineEmits, computed} from 'vue';

const emit = defineEmits(['update:modelValue']);
const props = defineProps({
  modelValue: {type: Boolean, default: () => false },
  name: {type: String, required: true},
  label: {type: String, default: () => '' }
});

const value = computed({
  get() {
    return props.modelValue;
  },
  set(newValue) {
    emit('update:modelValue', newValue);
  }
})
</script>

<template>
  <label class="ui-ctl ui-ctl-checkbox">
    <input v-model="value" :name="name" type="checkbox" class="ui-ctl-element">
    <div class="ui-ctl-label-text">{{ label }}</div>
  </label>
</template>