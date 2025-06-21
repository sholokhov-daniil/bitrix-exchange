<script setup>
import {defineProps, reactive, watch, onMounted} from 'vue';

const props = defineProps({
  type: {
    type: Object,
    default: () => ({
      action: 'sholokhov:exchange.IBlockController.getTypes',
      parameters: {},
    })
  },
  iBlock: {
    type: Object,
    default: () => ({
      action: '',
      parameters: {}
    })
  }
});

const data = reactive({
  types: [],
  selectedType: null,
  iBlocks: [],
  selectedIBlock: null,
})

onMounted(() => {
  loadTypes()
})

watch(
    () => data.selectedType,
    () => loadIBlocks()
)

const loadTypes = () => {
  BX.ajax.runAction(
      props.type.action,
      {
        method: 'POST',
        data: props.type.parameters
      }
  )
      .then(response => data.types = Array.isArray(response.data) ? response.data : [])
      .catch(response => {
        console.error(response);
        alert('Ошибка получения типов инфоблока')
      })
}

const loadIBlocks = () => {
  BX.ajax.runAction(
      props.iBlock.action,
      {
        method: 'POST',
        data: props.iBlock.parameters
      }
  )
      .then(response => data.iBlocks = Array.isArray(response.data) ? response.data : [])
      .catch(response => {
        console.error(response);
        alert('Ошибка получения информационных блоков')
      })
}
</script>

<template>
  <select v-model="data.selectedType">
    <option v-for="item in data.types" :key="item.id">
      [{{ item.id }}] {{ item.name }}
    </option>
  </select>

  <select v-model="data.selectedIBlock">
    <option v-for="item in data.iBlocks" :key="item.id">
      [{{ item.id }}] {{ item.name }}
    </option>
  </select>
</template>