<script setup>
import {defineProps, reactive, onMounted} from 'vue';
import GeneralBlock from "@/components/general-block.vue";
import TargetBlock from "@/components/target-block.vue";
import SourceBlock from "@/components/source-block.vue";
import MapBlock from "@/components/map-block.vue";
import {registration} from "@/view";

const props = defineProps({
  teleport: {type: Object, required: true},
  formContainer: {type: String, required: true},
  id: {type: Number, default: () => 0},
  signed: {type: String, required: false, default: () => ''},
});

const data = reactive({
  form: {
    general: {},
    target: {},
    source: {},
    map: [],
  },
  userForm: {},
});

onMounted(() => {
  for(let target in props.fields) {
    data.form[target] = {};
  }

  registration(
      'TEST_1',
      () => Component
  )

  initEvents();
});

const initEvents = () => {
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

  <Teleport v-if="teleport.source" :to="teleport.source">
    <SourceBlock v-model="data.form.source" />
  </Teleport>

  <Teleport v-if="teleport.map" :to="teleport.map">
    <MapBlock v-model="data.form.map" :target="data.form?.target" />
  </Teleport>
</template>