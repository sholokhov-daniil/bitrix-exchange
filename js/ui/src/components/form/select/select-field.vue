<script setup>
import {watch, reactive, onMounted} from 'vue';
import {getMessage, runAction} from "utils";

const model = defineModel();
const props = defineProps({
  name: {type: String, required: true},
  attributes: {type: Object, default: () => {}},
  api: {type: Object, default: () => {}},
  values: {type: Array, default: () => []},
});

const data = reactive({
  enums: []
})

onMounted(() => {
  if (props.api?.action) {
    queryEnums();
  } else if (props.values) {
    data.enums = props.values;
  }
});

watch(() => props.api, () => queryEnums(), {deep: true});

watch(() => props.values, (newValue) => data.enums = newValue);

const queryEnums = () => {
  runAction(props.api.action, props.api?.data || {})
      .then(response => {
        if (props.api?.callback) {
          response = props.api?.callback(response);
        }

        data.enums = Array.isArray(response.data) ? response.data : [];
      })
      .catch(response => {
        console.error(response);
        alert(`Ошибка получения значений списка`);
      })
}
</script>

<template>
  <div class="ui-ctl ui-ctl-after-icon ui-ctl-dropdown">
    <div class="ui-ctl-after ui-ctl-icon-angle">
      <select v-model="model" :name="name" class="ui-ctl-element" v-bind="attributes">
        <option value="">{{ getMessage('SHOLOKHOV_EXCHANGE_SETTINGS_SELECT_SELECTED_VALUE') }}</option>
        <option v-for="item in data.enums" :key="item.value" :value="item.value">{{ item.name }}</option>
      </select>
    </div>
  </div>
</template>