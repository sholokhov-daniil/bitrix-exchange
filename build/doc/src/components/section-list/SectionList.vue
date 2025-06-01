<script setup>
import {defineProps} from 'vue';
import Structure from "@/data/structure";

const props = defineProps({
  id: {type: String, required: true}
});

const getItems = (iterator) => {
  for (let item of iterator) {
    if (item.code === props.id) {
      return item.children || [];
    } else if (Array.isArray(item.children)) {
      let items = getItems(item.children);

      if (items && items.length) {
        return items;
      }
    }
  }

  return [];
}
</script>

<template>
  <div class="row row-cols-auto">
    <div v-for="event in getItems(Structure())" :key="event.title" class="col">
      <div class="card w-50">
        <div class="card-body">
          <h5 class="card-title">{{ event.title }}</h5>
          <router-link v-if="event.code" :to="{name: event.code}" class="btn btn-primary">Перейти</router-link>
        </div>
      </div>
    </div>
  </div>
</template>