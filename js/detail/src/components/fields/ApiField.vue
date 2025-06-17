<script setup>
import {defineProps, onMounted, ref, reactive} from "vue";
import {BXHtml} from "@/utils/content";

const props = defineProps({
  modelValue: {type: Object, required: true},
});

const data = reactive({
  errors: []
})

const container = ref(null);

onMounted(() => load());

const load = () => {
  if (props.modelValue?.api) {
    query()
        .then(response => BXHtml(container.value, response))
        .catch(response => data.errors = response.errors.map(error => error.message))
  } else if (container.value) {
    container.value = null;
  }
}

const query = () => BX.ajax.runAction(props.modelValue?.api, {method: 'POST'});
</script>

<template>
  <div ref="container"></div>
  <div v-if="data.errors.length">
    <p v-for="message in data.errors" :key="message">{{ message }}</p>
  </div>
</template>