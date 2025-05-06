<script setup>
import {defineProps} from 'vue';
import NodeTree from "@/components/menu/left/NodeTree.vue";

defineProps({
  items: {type: Array, default: () => []}
})
</script>

<template>
  <ul class="tree-list">
    <node-tree v-for="item in items" :key="item.name" :item="item"></node-tree>
  </ul>

  <div id="treeview1" class="treeview"></div>
</template>


<style scoped>
.tree-list ul {
  margin: 6px 0;
  padding-left: 16px;
}

.treeview .list-group-item {
  cursor: pointer
}

.treeview span.indent {
  margin-left: 10px;
  margin-right: 10px
}

.treeview span.icon {
  width: 12px;
  margin-right: 5px
}

.treeview .node-disabled {
  color: silver;
  cursor: not-allowed
}

.node-treeview1 {
}

.node-treeview1:not(.node-disabled):hover {
  background-color: #F5F5F5;
}
</style>