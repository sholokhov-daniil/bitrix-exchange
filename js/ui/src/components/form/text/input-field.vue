<script setup>
import {defineProps, defineModel} from 'vue';

const model = defineModel({default: ''});
const props = defineProps({
  name: {type: String, required: true},
});

</script>

<template>
  <div class="ui-ctl ui-ctl-checkbox">
    <input v-model="model" :name="name" type="text" class="ui-ctl-element" >
  </div>
</template>