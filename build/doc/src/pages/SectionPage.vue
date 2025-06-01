<script setup>
import {useRoute} from "vue-router";
import SectionList from "@/components/section-list/SectionList.vue";
const route = useRoute();
</script>

<template>
  <article>
    <h1 v-if="route.meta?.seo?.h1">{{ route.meta?.seo?.h1 }}</h1>
    <p v-if="route.meta?.seo?.description" v-html="route.meta?.seo?.description" />
    <section-list :id="route.name" />
  </article>
</template>