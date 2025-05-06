<script setup>
import {defineProps} from 'vue';

defineProps({
  items: {type: Array, default: () => []},
});

const scroll = (hash) => document.getElementById(hash)?.scrollIntoView({behavior: 'smooth'});
</script>

<template>
  <ul class="table-contents">
    <li v-for="row in items" :key="row.title" @click="scroll(row.hash)">
      {{ row.title }}
    </li>
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