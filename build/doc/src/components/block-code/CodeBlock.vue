<script setup>
import {defineProps} from 'vue';
import {VCodeBlock} from "@wdns/vue-code-block";

defineProps({
  code: {type: String, required: true},
  lang: {type: String, default: () => 'php'}
});
</script>

<template>
  <VCodeBlock
      :code="code"
      :lang="lang"
      highlightjs
      theme="stackoverflow-dark"
      v-bind="$attrs"
  />
</template>