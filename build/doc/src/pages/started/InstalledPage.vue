<script setup>
import CodeBlock from "@/components/block-code/CodeBlock.vue";
import MainContainer from "@/components/container/MainContainer.vue";
</script>

<template>
  <main-container>
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
  </main-container>

  <main-container>
    <template #header>
      <h2 id="installed">Установка</h2>
    </template>
    <template #default>
      <p>Обмен доступен на Packagist <a href="https://packagist.org/packages/sholokhov/bitrix-exchange" target="_blank">sholokhov/bitrix-exchange</a> и доступен в пакетном менеджере  <a href="http://getcomposer.org/" target="_blank">Composer</a></p>
      <CodeBlock
          code="composer require sholokhov/bitrix-exchange"
          lang="bash"
      />
    </template>
  </main-container>
</template>