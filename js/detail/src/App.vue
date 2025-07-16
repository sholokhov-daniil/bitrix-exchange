<script setup>
import {defineProps, reactive, onMounted} from 'vue';
import GeneralBlock from "@/components/general-block.vue";
import TargetBlock from "@/components/target-block.vue";

const props = defineProps({
  teleport: {type: Object, required: true},
  formContainer: {type: String, required: true},
  signed: {type: String, required: false, default: () => ''},
});

const data = reactive({
  form: {
    general: {},
    target: {},
    source: {},
    map: {},
  }
});

onMounted(() => initEvents());

const initEvents = () => {
  console.log(props);
  const form = document.querySelector(props.formContainer);
  if (form) {
    form.addEventListener('submit', (e) => submit(e));
  }
}

const submit = (event) => {
  event.preventDefault();
  event.stopImmediatePropagation();

  // BX.adminPanel.closeWait()
}
</script>

<template>
  {{ data.form }}

  <Teleport v-if="teleport.general" :to="teleport.general">
    <GeneralBlock v-model="data.form.general" />
  </Teleport>

  <Teleport v-if="teleport.target" :to="teleport.target">
    <TargetBlock v-model="data.form.target" />
  </Teleport>
</template>