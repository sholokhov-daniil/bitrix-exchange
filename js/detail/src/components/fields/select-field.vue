<script setup>
import {computed, defineEmits, defineProps, watch, reactive, onMounted} from 'vue';
import {getMessage} from "@/utils/message";

const emit = defineEmits(['update:modelValue']);
const props = defineProps({
  modelValue: {type: String, default: () => null },
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

const value = computed({
  get() {
    return props.modelValue;
  },
  set(newValue) {
    emit('update:modelValue', newValue)
  }
});

watch(() => props.api, () => queryEnums(), {deep: true});

watch(() => props.values, (newValue) => data.enums = newValue);

const queryEnums = () => {
  BX.ajax.runAction(props.api.action, {data: props.api?.data || {}})
      .then(response => {
        if (props.api?.callback) {
          response = props.api?.callback(response);
        }

        data.enums = Array.isArray(response.data) ? response.data : [];
      })
      .catch(response => {
        console.error(response);
        alert(`Ошибка получения значений списка "${options.title}"`);
      })
}
</script>

<template>
  <div class="ui-ctl ui-ctl-after-icon ui-ctl-dropdown">
    <div class="ui-ctl-after ui-ctl-icon-angle">
      <select v-model="value" :name="name" class="ui-ctl-element" v-bind="attributes">
        <option value="">{{ getMessage('SHOLOKHOV_EXCHANGE_SETTINGS_SELECT_SELECTED_VALUE') }}</option>
        <option v-for="item in data.enums" :key="item.value" :value="item.value">{{ item.name }}</option>
      </select>
    </div>
  </div>
</template>