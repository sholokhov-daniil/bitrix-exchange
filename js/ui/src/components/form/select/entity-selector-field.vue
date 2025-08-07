<script setup>
import {defineProps, defineEmits, ref, onMounted, reactive, watch, defineModel} from 'vue'
import {getMessage} from "utils"

const model = defineModel();
const container = ref()
const emit = defineEmits([
  'onTagAdd',
  'onBeforeTagAdd',
  'onAfterTagAdd',
  'onBeforeTagRemove',
  'onTagRemove',
  'onAfterTagRemove',
  'onContainerClick',
  'onInput',
  'onBlur',
  'onKeyUp',
  'onEnter',
  'onMetaEnter',
  'onKeyDown',
  'onAddButtonClick',
  'onCreateButtonClick',
  'afterRender'
])
const props = defineProps({
  options: {type: Object},
})
const data = reactive({
  value: '',
  options: {},
  addButtonCaption: '',
})

onMounted(() => {
  setOptions();
  render();
})

watch(
    () => model.value,
    (newValue) => {
      if (newValue !== data.value) {
        render();
      }
    }
)

const setOptions = () => {
  data.options = {...props.options};

  if (data.options?.addButtonCaption) {
    data.options.addButtonCaption = getMessage(data.options.addButtonCaption) || data.options.addButtonCaption;
  }

  data.options.events = {
    onTagAdd: (e) => emit('onTagAdd', e),
    onBeforeTagAdd: (e) => emit('onBeforeTagAdd', e),
    onAfterTagAdd: (e) => {
      updateModelValue(e);
      emit('onAfterTagAdd', e);
    },
    onBeforeTagRemove: (e) => emit('onBeforeTagRemove', e),
    onTagRemove: (e) => emit('onTagRemove', e),
    onAfterTagRemove: (e) => {
      updateModelValue(e);
      emit('onAfterTagRemove', e);
    },
    onContainerClick: (e) => emit('onContainerClick', e),
    onInput: (e) => emit('onInput', e),
    onBlur: (e) => emit('onBlur', e),
    onKeyUp: (e) => emit('onKeyUp', e),
    onEnter: (e) => emit('onEnter', e),
    onMetaEnter: (e) => emit('onMetaEnter', e),
    onKeyDown: (e) => emit('onKeyDown', e),
    onAddButtonClick: (e) => emit('onAddButtonClick', e),
    onCreateButtonClick: (e) => emit('onCreateButtonClick', e),
  };
}

const render = () => {
  BX.loadExt('ui.entity-selector')
      .then(() => {
        const selector = new BX.UI.EntitySelector.TagSelector(data.options);
        if (container.value) {
          container.value.innerHTML = '';
          const promise = new Promise((resolve) => {
            selector.renderTo(container.value)
            resolve(selector);
          });

          promise.then(result => emit('afterRender', result));
        }
      })
}

const updateModelValue = (event) => {
  let value;
  const selector = event.target;

  if (selector.isMultiple()) {
    value = [];
    event.target.getTags().forEach(tag => value.push(tag.id));
  } else {
    value = selector.getTags()[0]?.id ?? '';
  }

  model.value = data.value = value;
}
</script>

<template>
  <div ref="container"></div>
</template>