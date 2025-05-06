<script setup>
import {defineProps} from 'vue';

defineProps({
  item: {type: Array, default: () => []}
});
</script>

<template>
  <li class="node-tree">
    <a v-if="item.link" :href="item.link" target="_blank">{{item.title}}</a>
    <router-link
        v-else-if="item.code"
        :to="{name: item.code}"
        exact-active-class="router-link-active"
    >
      {{ item.title }}
    </router-link>
    <span v-else class="label">{{ item.title }}</span>

    <ul v-if="item.children && item.children.length">
      <node-tree v-for="children in item.children" :key="children.title" :item="children" />
    </ul>
  </li>
</template>

<style scoped>
.router-link-active {
  font-weight: bold;
}
</style>