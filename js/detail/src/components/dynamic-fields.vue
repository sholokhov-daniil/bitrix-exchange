<template v-if="type">
  <Component
      v-if="view"
      :is="view"
      v-model="model"
      v-bind="attr" />
  <div v-else ref="externalContainerRef">
  </div>
</template>

<script setup>
import {computed, ref, watch, useAttrs} from 'vue';
import {defineModel, defineProps} from 'vue';
import {internalView} from "@/view/factory";
import {view as ExternalView} from "@/view";

const attr = useAttrs();
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

      ExternalView(
          model.type,
          {
            container: externalContainerRef,
            data: model,
            ...attr
          }
      );
    }
)
</script>