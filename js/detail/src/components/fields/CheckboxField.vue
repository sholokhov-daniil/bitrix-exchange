<script setup>
import {defineProps, defineEmits, computed} from 'vue';
import InputField from "@/components/fields/InputField.vue";

const props = defineProps({
  modelValue: {type: [String, Number, Array], required: true},
  field: {type: Object, required: true},
});

const emit = defineEmits(['update:modelValue']);

const settings = computed(() => {
  const data = {...props.field};
  data.type = 'checkbox';

  return data;
});

const value = computed({
  get() {
    return props.modelValue;
  },
  set(newValue) {
    emit('update:modelValue', newValue)
  }
})

</script>

<template>
  <InputField v-model="value" :field="settings" />
</template>