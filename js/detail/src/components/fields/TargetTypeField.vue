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
    api: 'sholokhov:exchange.UI.Provider.Target.TypeController.getAll',
    value: null,
  }
});

const emit = defineEmits(['selected']);

watch(() => data.field.value, (newValue) => emit('selected', newValue));
</script>

<template>
  <InputField v-if="hidden" v-model="data.field" />
  <SelectField v-else v-model="data.field" />
</template>