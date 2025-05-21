<script setup>
import {defineProps, watch, onMounted} from 'vue';
import {useRoute} from "vue-router";
import NodeTree from "@/components/table-contents/NodeTree.vue";

const route = useRoute();

defineProps({
  items: {type: Array, default: () => []},
});

onMounted(() => {
  if (route.params?.hash) {
    scroll(route.params.hash);
  }
})

watch(
    () => route.path,
    () => scroll(route.params.hash)
)
const scroll = (hash) => {
  setTimeout(
      () => document.getElementById(hash)?.scrollIntoView({behavior: 'smooth'}),
      400
  )
}
</script>

<template>
  <ul class="table-contents">
    <node-tree v-for="row in items" :key="row.title" :item="row" @click="scroll"/>
  </ul>
</template>

<style scoped>
.table-contents li {
  color: blue;
  cursor: pointer;
}

.table-contents li:hover {
  text-decoration: underline;
  color: #0056b3;
}
</style>