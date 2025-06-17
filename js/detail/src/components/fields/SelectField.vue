<script setup>
import {defineProps, computed, onMounted, reactive, watch, defineEmits} from 'vue';

const props = defineProps({
  modelValue: {type: Object, required: true},
})

const data = reactive({
  enums: [],
  errors: [],
});

const emit = defineEmits(['update:modelValue']);

onMounted(() => load());
watch(() => props.modelValue.api, () => load());

const attributes = computed(() => (props.modelValue?.attributes || []).join(' '));

const value = computed(
    () => props.modelValue.value,
    (newValue) => emit('update:modelValue', newValue)
);

const load = () => {
  if (props.modelValue?.api) {
    query()
        .then(response => data.enums = Array.isArray(response.data) ? response.data : [])
        .catch(response => data.errors = response.errors.map(error => error.message));
  }
}

const query = () => BX.ajax.runAction(props.modelValue?.api, {method: 'POST'});
</script>

<template>
  <select v-model="value" v-bind="attributes">
    <option v-for="option in data.enums" :key="option.value" :value="option.value">{{ option.name }}</option>
  </select>
</template>