<script setup>
import {defineProps, defineEmits, computed} from 'vue';

const emit = defineEmits(['update:modelValue']);
const props = defineProps({
  modelValue: {type: String, default: () => '' },
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
});

const generate = () => {
  BX.ajax.runAction('sholokhov:exchange.SecureController.generateHash')
      .then(response => emit('update:modelValue', response.data))
      .catch(() => alert('Ошибка генерации идентификатора'))
}
</script>

<template>
  <div class="ui-ctl ui-ctl-textbox">
    <input v-model="value" type="text" class="ui-ctl-element">
    <span class="hash-text-generator" @click="generate">Сгенерировать</span>
  </div>
</template>