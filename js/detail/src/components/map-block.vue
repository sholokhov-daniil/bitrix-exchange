<script setup>
import {defineModel, defineProps, reactive, watch, onMounted} from 'vue';
import {Alert, GridRow} from "ui";
import {runAction} from "utils";
import CreatePopup from './create-map-field.vue';
// import MapField from "@/components/fields/map-field.vue";

const model = defineModel();

const props = defineProps({
  target: {type: String, default: () => ''}
});

const data = reactive({
  templates: {},
  errors: [],
  openAddPopup: false,
});

onMounted(() => {
  if (!props.target) {
    showEmptyError();
  }
})

watch(
    () => props.target,
    (newValue) => {
      if (!newValue) {
        showEmptyError();
        return;
      }

      data.errors = [];

      if (data.templates[newValue]) {
        return;
      }

      data.templates[newValue] = {};
      loadTemplates(newValue)
          .catch(response => data.errors = response.errors.map(error => error.message))
    }
)

const showEmptyError = () => data.errors = ["Необходимо выбрать тип обмена"];

const loadTemplates = async (target) => {
  const response = await runAction('sholokhov:exchange.MapController.getTemplates', {target: target});

  if (!Array.isArray(response.data)) {
    data.errors.push('Ошибка загрузки данных');
    return;
  }

  response.data.forEach(map => data.templates[target][map.code] = map.fields || [])
}

const openAddPopup = () => {
  console.log('OPEN');
  data.openAddPopup = true
};
</script>

<template>
  <GridRow>
    <template #content>
      Значение: {{ model }}<br><br>
      Тип: {{ target }}<br><br>
      Шаблоны: {{ data.templates }} <br><br>

      <template v-if="target">
        Тут выводим список доступных типов карт
        <button type="button" @click="openAddPopup">Добавить</button>
      </template>
      <Alert v-else v-for="message in data.errors" :key="message" type="danger">
        {{ message }}
      </Alert>
    </template>
  </GridRow>

  <CreatePopup
      :show="data.openAddPopup"
      @closed="data.openAddPopup = false"
      @opened="data.openAddPopup = true"
  />
</template>