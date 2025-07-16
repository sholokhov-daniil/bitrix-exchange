<script setup>
import {defineProps, defineEmits, computed} from 'vue';
import CheckBox from '@/components/fields/checkbox-field.vue';
import InputHash from "@/components/fields/input-hash-field.vue";
import {getMessage} from "@/utils/message";

const emit = defineEmits(['update:modelValue']);
const props = defineProps({
  modelValue: {type: Object, required: true}
});

const store = computed({
  get() {
    return props.modelValue;
  },
  set(newValue) {
    emit('update:modelValue', newValue)
  }
});
</script>

<template>
  <tr>
    <td width="40%"></td>
    <td width="60%">
      <CheckBox
          v-model="store.active"
          :label="getMessage('SHOLOKHOV_EXCHANGE_SETTINGS_ENTITY_UI_GENERAL_TITLE_FIELD_ACTIVE')"
          name="active"
      />
    </td>
  </tr>

  <tr>
    <td width="40%">
      {{ getMessage('SHOLOKHOV_EXCHANGE_SETTINGS_ENTITY_UI_GENERAL_TITLE_FIELD_HASH') }}
    </td>
    <td width="60%">
      <InputHash v-model="store.hash" name="hash" />
    </td>
  </tr>
</template>