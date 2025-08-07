<script setup>
import {defineModel, defineProps, reactive, watch, onMounted, computed} from 'vue';
import {Alert, GridRow} from "ui";
import {runAction} from "utils";
import DynamicFields from "@/components/dynamic-fields.vue";

const model = defineModel();

const props = defineProps({
  target: {type: String, default: () => ''}
});

const data = reactive({
  templates: {},
  errors: [],
});

onMounted(() => {
  if (!props.target) {
    showEmptyError();
  }
})

const templates = computed(() => data.templates[target] || []);

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

const add = () => {
  if (!Array.isArray(model.map)) {
    model.map = [];
  }

  model.map.push({
    type: templates.value[0].
  });
}
</script>

<template>
  <GridRow>
    <template #content>
      Значение: {{ model }}<br><br>
      Тип: {{ target }}<br><br>
      Шаблоны: {{ data.templates }} <br><br>

      <template v-if="target">
        Тут выводим список доступных типов карт
        <button type="button" @click="add">Добавить</button>
      </template>
      <Alert v-else v-for="message in data.errors" :key="message" type="danger">
        {{ message }}
      </Alert>
    </template>
  </GridRow>

  <DynamicFields
    v-for="(field, index) in model.maps"
    :key="index"
    v-model="model.maps[field.type]"
    type="field.type"
  />
</template>