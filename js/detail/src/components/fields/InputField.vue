<script setup>
import {defineProps, computed, defineEmits} from 'vue';

const props = defineProps({
  modelValue: {type: [String, Number, Array], required: true},
  field: {type: Object, required: true},
});

const emit = defineEmits(['update:modelValue']);

const type = computed(() => props.field?.type || 'text');
const attributes = computed(() => props.field?.attributes || {});
const value = computed({
  get() {
    return props.modelValue;
  },
  set(newValue) {
    emit('update:modelValue', newValue);
  }
});
</script>

<template>
  <div>
    <div class="title">{{ field?.title }}</div>
    <div class="value">
      <input :type="type" v-bind="attributes" v-model="value" />
    </div>
  </div>
</template>