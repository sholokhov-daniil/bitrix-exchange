<template v-if="type">
  <Component v-if="view" :is="view" v-model="model"/>
  <div v-else ref="externalContainerRef">
  </div>
</template>

<script setup>
import {computed, ref, watch} from 'vue';
import {defineModel, defineProps} from 'vue';
import {internalView} from "@/view/factory";

const model = defineModel({default: {}});
const props = defineProps({
  type: {type: String, default: () => ''}
});

const externalContainerRef = ref();

const view = computed(() => internalView(props.type));
watch(
    () => props.type,
    (newValue) => {
      if (!newValue || view.value) {
        return;
      }

      const registry = Sholokhov.Exchange.Detail.entityRegistry;

      console.log({
        type: props.type,
        registry: registry
      });

      if (registry.has(props.type)) {
        const render = registry.get(props.type);
        render({
          container: externalContainerRef,
          data: model,
        })
      }
    }
)
</script>