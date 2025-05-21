<script setup>
import {defineProps, defineEmits} from 'vue';
import {useRoute} from "vue-router";
import {RouterLink} from "vue-router";

const route = useRoute();
const emit = defineEmits(['click']);

defineProps({
  item: {type: Array, default: () => []}
})

const to = (item) => {
  return {
    name: route.name,
    params: {
      hash: item.hash
    }
  };
}

const click = (hash) => {
  if (route.params.hash === hash) {
    emit('click', hash);
  }
}
</script>

<template>
  <li>
    <router-link :to="to(item)" @click="click(item.hash)">{{item.title}}</router-link>

    <ul v-if="item.children && item.children.length">
      <node-tree v-for="children in item.children" :key="children.title" :item="children" @click="emit('click', $event)"/>
    </ul>
  </li>
</template>