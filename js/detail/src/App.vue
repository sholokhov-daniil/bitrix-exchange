<script setup>
import {defineProps} from 'vue';
import TargetComponent from "@/components/TargetComponent.vue";
import SourceComponent from "@/components/SourceComponent.vue";

defineProps({
  id: {type: Number, required: true},
  action: {type: String, required: true}
});
</script>

<template>
  <TargetComponent :id="id" />

  <SourceComponent :id="id" />
</template>