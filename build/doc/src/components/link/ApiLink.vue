<script setup>
import {defineProps, computed} from 'vue';

const props = defineProps({
  path: {type: String, required: true}
});

const href = computed(() => './api/' + props.path);
</script>

<template>
  <a :href="href" target="_blank">
    <slot></slot>
  </a>
</template>