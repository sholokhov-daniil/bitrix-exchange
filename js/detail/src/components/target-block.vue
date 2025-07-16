<script setup>
import {defineProps, defineEmits, computed, reactive, watch} from 'vue';
import {getMessage} from "@/utils/message";
import SelectField from '@/components/fields/select-field.vue';

const emit = defineEmits(['update:modelValue']);
const props = defineProps({
  modelValue: {type: Object, required: true}
});

reactive({
  targets: [],
  fields: [],
});

const store = computed({
  get() {
    return props.modelValue;
  },
  set(newValue) {
    emit('update:modelValue', newValue)
  }
});
const fieldApi = computed(() => ({
  action: 'sholokhov:exchange.EntityController.getByType',
  data: {code: 'target'},
  callback: normalizeTypeResponse,
}));

watch(() => props.modelValue.type, (newValue) => loadFields(newValue));

const normalizeTypeResponse = (response) => {
  console.log(response);

  if (Array.isArray(response.data)) {
    response.data = response.data.map(type => ({
      value: type.CODE,
      name: type.NAME,
    }))
  }

  return response;
}

const loadFields = (type) => {
  BX.ajax.runAction(
      'sholokhov:exchange.EntityController.getFields',
      {
        data: {code: type}
      }
  )
      .then(response => {
        console.log(response);
      })
      .catch(() => console.error(response))
}
</script>

<template>
  <tr>
    <td width="40%">{{ getMessage('SHOLOKHOV_EXCHANGE_SETTINGS_ENTITY_UI_TARGET_TITLE_FIELD_TYPE') }}</td>
    <td width="60%">
      <SelectField
        v-model="store.type"
        :api="fieldApi"
        name="type"
      />
    </td>
  </tr>
</template>