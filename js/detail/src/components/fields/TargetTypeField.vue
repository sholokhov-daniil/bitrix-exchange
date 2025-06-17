<script setup>
import {reactive, defineEmits, defineProps, watch} from "vue";
import SelectField from "@/components/fields/SelectField.vue";
import InputField from "@/components/fields/InputField.vue";

defineProps({
  hidden: {type: Boolean, default: () => false}
})
const data = reactive({
  field: {
    title: 'Тип обмена: ',
    type: 'hidden',
    attributes: {
      name: 'general[type]',
    },
    api: {
      action: 'sholokhov:exchange.EntityController.getByType',
      data: {
        code: 'target'
      },
      callback: function(response) {
        if (!Array.isArray(response.data)) {
          return [];
        }

        response.data = response.data.map(field => ({
          value: field.CODE,
          name: field.NAME,
        }));

        return response;
      }
    },
    value: null,
  }
});

const emit = defineEmits(['selected']);

watch(() => data.field.value, (newValue) => emit('selected', newValue));
</script>

<template>
  <InputField
      v-if="hidden"
      v-model="data.field.value"
      :field="data.field"
  />
  <SelectField
      v-else
      v-model="data.field.value"
      :field="data.field"
  />
</template>