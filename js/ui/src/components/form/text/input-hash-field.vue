<script setup>
import {defineProps, defineModel} from 'vue';
import {runAction, getMessage} from "utils";

const model = defineModel({default: ''});
const props = defineProps({
  label: {type: String, default: () => '' }
});

const generate = () => {
  runAction('sholokhov:exchange.SecureController.generateHash')
      .then(response => model.value = response.data)
      .catch(() => alert('Ошибка генерации идентификатора'))
}
</script>

<template>
  <div class="ui-ctl ui-ctl-textbox">
    <input v-model="model" type="text" class="ui-ctl-element">
    <span class="hash-text-generator" @click="generate">
      {{ getMessage('SHOLOKHOV_EXCHANGE_UI_GENERATE') }}
    </span>
  </div>
</template>