<script setup>
import {defineProps, reactive} from 'vue';
import GeneralFields from "@/store/generalFields";
import {getComponent} from "@/utils/component";
import TargetTypeField from "@/components/fields/TargetTypeField.vue";

defineProps({
  id: {type: Number, default: () => 0}
});

const data = reactive(GeneralFields);

const selectedTarget = (target) => {
  BX.ajax.runAction(
      'sholokhov:exchange.EntityController.getFields',
      {
        data: {
          code: target
        }
      }
  )
      .then(response => data.userFields = Array.isArray(response.data) ? response.data : [])
      .catch(response => console.error(response));
}
</script>

<template>
  <input type="text" name="general[name]">

  <h2>Основные настройки</h2>

  <TargetTypeField @selected="selectedTarget" />

  <div v-for="(field, key) in data.fields" :key="key">
    <Component
      :is="field.component"
      v-model="field.value"
      :field="field.settings"
    />
  </div>

  <Component
    v-for="(field, key) in data.userFields"
    :key="key"
    :is="getComponent(field)"
    v-model="data.userFields[key].value"
    :field="data.userFields[key]"
  />
</template>