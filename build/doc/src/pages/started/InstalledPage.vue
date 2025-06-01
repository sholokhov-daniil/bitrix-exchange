<script setup>
import CardContainer from "@/components/container/CardContainer.vue";
</script>

<template>
  <card-container>
    <template #header>
      <h2>Почему именно мы</h2>
    </template>
    <template #default>
      <p>
        Модуль обмена позволяет производить импорт\экспорт из любых в любые сущности.
        Позволяет легко настроить обмен указав источник данных и сопоставить значения (откуда - куда).
        Библиотека позиционирует себя как расширяемое решение и предоставляет широкий набор вмешательства в процесс обмена без изменения штатной логики.
        Если стандартные предложенные решения не подходят, то разработчику предоставляется реализовать свою логику.
      </p>

      <p><strong>Минимальные требования:</strong> </p>
      <ul>
        <li>Минимальная версия PHP 8.2</li>
        <li>Минимальная версия редакция bitrix "Старт"</li>
      </ul>
    </template>
  </card-container>

  <card-container>
    <template #header>
      <h2 id="installed">Установка</h2>
    </template>

    <template #default>
      <p>Переходим в одну из указанных директорий: </p>
      <ul>
        <li>/local/modules</li>
        <li>/bitrix/modules</li>
      </ul>

      <p>
        Создаем директорию с названием: <b>sholokhov.exchange</b> и переходив в нее.
        <br>
        Скачиваем содержимое модуля через git или архив. После скачивания заходим в маркетплейс битрикса и устанавливаем
      </p>
    </template>
  </card-container>
</template>