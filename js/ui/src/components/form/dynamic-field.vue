<script setup>
import {defineProps, computed, watch} from 'vue'
import {EventManager} from 'utils'
import {getFormComponent} from "../../utils/factory/component"

const model = defineModel({default: ''});
const props = defineProps({
  view: {type: String, required: true},
  entity: {type: String, default: () => ''},
  options: {type: Object, default: () => {}}
})

watch(
    () => model.value,
    (newValue) => EventManager.emit('sholokhov.exchange:uiUpdateValueDynamicField', {value: newValue, entity: props.entity})
)

const template = computed(() => getFormComponent(props.view))
</script>

<template>
  <Component :is="template" v-model="model" v-bind="options" />
</template>