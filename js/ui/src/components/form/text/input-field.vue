<script setup>
import {defineModel} from 'vue';

const model = defineModel({default: ''});
</script>

<template>
  <div class="ui-ctl ui-ctl-checkbox">
    <input
        v-model="model"
        type="text"
        class="ui-ctl-element"
        v-bind="$attrs"
    >
  </div>
</template>