<script setup>
import CodeBlock from "@/components/block-code/CodeBlock.vue";
import Started from "@/data/codes/php/started";
import MainContainer from "@/components/container/MainContainer.vue";
</script>

<template>
  <main-container>
    <template #header>
      <h1>Создание первого обмена</h1>
    </template>

    После установки пакета через Composer вы готовы создавать обмены данных.
    Создадим свой первый обмен

    <code-block :code="Started" />
  </main-container>
</template>