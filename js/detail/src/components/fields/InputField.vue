<script setup>
import {defineProps, computed, defineEmits} from 'vue';

const props = defineProps({
  modelValue: {type: Object, required: true}
});

const emit = defineEmits(['update:modelValue']);

const type = computed(() => props.modelValue?.type || 'text');
const attributes = computed(() => (props.modelValue?.attributes || []).join(' '));
const value = computed(
    () => props.modelValue.value,
    (newValue) => emit('update:modelValue', newValue),
);
</script>

<template>
  <label>
    {{ modelValue?.title }}
    <input :type="type" v-bind="attributes" v-model="value">
  </label>
</template>