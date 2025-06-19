<script setup>
import {defineProps, computed, onMounted, reactive, watch, defineEmits} from 'vue';
import {queryField} from "@/utils/http/field";

const props = defineProps({
  modelValue: {type: [String, Number, Array], required: true},
  field: {type: Object, required: true},
})

const data = reactive({
  enums: [],
  errors: [],
});

const emit = defineEmits(['update:modelValue']);

onMounted(() => load());
watch(() => props.field?.api, () => load());

const attributes = computed(() => props.field?.attributes || {});

const value = computed({
  get() {
    return props.modelValue
  },
  set(newValue) {
    emit('update:modelValue', newValue)
  }
});

const load = () => {
  queryField(props.field)
      .then(response => {
        if (props.field?.api?.callback) {
          response = props.field.api?.callback(response);
        }
        data.enums = Array.isArray(response.data) ? response.data : [];
      })
      .catch(response => data.errors = response.errors.map(error => error.message));
}
</script>

<template>
  <div>
    <div class="title">{{ field?.title }}</div>
    <div class="value">
      <select v-model="value" v-bind="attributes">
        <option value="">-- Выберите значение --</option>
        <option v-for="option in data.enums" :key="option.value" :value="option.value">{{ option.name }}</option>
      </select>
    </div>
  </div>
</template>