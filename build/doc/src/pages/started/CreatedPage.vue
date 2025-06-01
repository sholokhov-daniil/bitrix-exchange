<script setup>
import CodeBlock from "@/components/block-code/CodeBlock.vue";
import MainContainer from "@/components/container/MainContainer.vue";
import {created, iterable} from "@/data/codes/php/started/create";
</script>

<template>
  <main-container>
    <template #header>
      <h1>Создание первого обмена</h1>
    </template>

    <p>
      После установки пакета через Composer вы готовы создавать обмены данных.
      <br>
      Ранее мы ознакомились как производить конфигурацию обмена и настройке маршрутизации, на данном этапе разберем какой формат данных должен быть у импортируемых данных.
    </p>

    <p>
      Хранилище импортируемых данных должно быть <a href="https://www.php.net/manual/ru/language.types.iterable.php" target="_blank">итерируемым</a>.
      <br>
      Разберем примеры основных итерируемых значений
    </p>

    <code-block :code="iterable" />

    <p>Рассмотрим создание простого импорта</p>
    <code-block :code="created" />
  </main-container>
</template>