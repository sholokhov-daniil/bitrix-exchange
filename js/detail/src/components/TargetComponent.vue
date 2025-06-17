<script setup>
import {defineProps, reactive} from 'vue';
import GeneralFields from "@/store/generalFields";
import {getComponent} from "@/utils/component";
import TargetTypeField from "@/components/fields/TargetTypeField.vue";

defineProps({
  id: {type: Number, default: () => 0}
});

const data = reactive(GeneralFields);
const selectedType = (type) => {
  BX.ajax.runAction(
      'sholokhov:exchange.UI.Provider.Target.FieldController.getAll',
      {
        data: {
          type: type
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

  <TargetTypeField @selected="selectedType" />

  <Component
    v-for="(field, key) in data.fields"
    :key="key"
    :is="field.component"
    v-model="field.settings"
  />

  <Component
    v-for="(field, key) in data.userFields"
    :key="key"
    :is="getComponent(field)"
    v-model="data.userFields[key]"
  />
</template>