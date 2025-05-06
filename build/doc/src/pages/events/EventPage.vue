<script setup>
import {useRoute} from "vue-router";
import EventExample from "@/data/codes/php/events";
import CodeBlock from "@/components/block-code/CodeBlock.vue";
import SectionList from "@/components/section-list/SectionList.vue";

const route = useRoute();
</script>

<template>
  <article class="mb-5">
    <h1>{{ route?.meta?.seo?.h1 }}</h1>

    <section-list :id="route.name" />
  </article>

  <article class="mb-5">
    <h2>Подписка на события</h2>
    <p>Все события модуля регистрируются в <a href="https://dev.1c-bitrix.ru/api_d7/bitrix/main/EventManager/index.php" target="_blank">bitrix</a></p>
    <p>Все события вызываются от модуля <b>sholokhov.exchange</b></p>
  </article>

  <article class="mb-5">
    <h2>Пример</h2>
    <CodeBlock :code="EventExample" />
  </article>
</template>