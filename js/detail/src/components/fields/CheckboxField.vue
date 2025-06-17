<script setup>
import {defineProps, defineEmits, computed} from 'vue';
import InputField from "@/components/fields/InputField.vue";

const props = defineProps({
  modelValue: {type: Object, required: true}
});

const emit = defineEmits(['update:modelValue']);
const value = computed(
    () => props.modelValue.value,
    (newValue) => emit('update:modelValue', newValue)
);

</script>

<template>
  <InputField v-model="value" type="checkbox" />
</template>