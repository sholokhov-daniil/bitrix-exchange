<script setup>
import {defineProps, reactive, onMounted} from 'vue';
import GeneralBlock from "@/components/general-block.vue";
import DynamicFields from "@/components/dynamic-fields.vue";
import TargetBlock from "@/components/target-block.vue";

const props = defineProps({
  teleport: {type: Object, required: true},
  formContainer: {type: String, required: true},
  signed: {type: String, required: false, default: () => ''},
  fields: {type: Object, default: () => {}}
});

const data = reactive({
  form: {
    general: {},
    target: {},
    source: {},
    map: {},
  },
  userForm: {},
});

onMounted(() => {
  for(let target in props.fields) {
    data.form[target] = {};
  }

  initEvents()
});

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

// const updateUserData = () => {
//   data.userForm = {};
//
//   const form = Sholokhov.Exchange.Detail.getExternalRegistry().getAll();
//   for(let id in form) {
//     data.userForm[id] = form[id];
//   }
// }
</script>

<template>
  <Teleport v-if="teleport.general" :to="teleport.general">
    <GeneralBlock v-model="data.form.general" />
  </Teleport>

  <Teleport v-if="teleport.target" :to="teleport.target">
    <TargetBlock v-model="data.form.target" />
  </Teleport>

  <Teleport v-for="(iterator, target) in fields" :key="target" :to="target">
    <DynamicFields v-model="data.form[target]" :fields="iterator" />
  </Teleport>
</template>