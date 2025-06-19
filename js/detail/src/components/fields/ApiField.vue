<script setup>
import {defineProps, onMounted, ref, reactive, watch} from "vue";
import {BXHtml} from "@/utils/content";
import {queryField} from "@/utils/http/field";

const props = defineProps({
  modelValue: {type: [String, Number, Array], required: true},
  field: {type: Object, required: true},
});

const data = reactive({
  errors: []
})

const container = ref(null);

onMounted(() => load());

watch(
    () => props.field.api,
    () => {
      console.log('LOAD')
    },
    {deep: true}
);

const load = () => {
  queryField(props.field)
      .then(response => BXHtml(container.value, response))
      .catch(response => data.errors = response.errors.map(error => error.message))
}
</script>

<template>
  {{field}}
  <div ref="container"></div>
  <div v-if="data.errors.length">
    <p v-for="message in data.errors" :key="message">{{ message }}</p>
  </div>
</template>