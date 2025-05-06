<script setup>
import Started from "@/data/codes/php/started";
import CodeBlock from "@/components/block-code/CodeBlock.vue";
</script>

<template>
  <h1>Использование обмена</h1>
  <ul>
    <li>Установка</li>
    <li>Минимальные требования</li>
    <li>Описание</li>
    <li>Карта обмена</li>
  </ul>

  <article>
    <h2>Установка</h2>
    <p>Обмен доступен на Packagist <a href="https://packagist.org/packages/sholokhov/bitrix-exchange" target="_blank">sholokhov/bitrix-exchange</a> и поэтому устанавливается через <a href="http://getcomposer.org/" target="_blank">Composer</a></p>

    <CodeBlock
      code="composer require sholokhov/bitrix-exchange"
      lang="bash"
    />
  </article>

  <article>
    <h2>Минимальные требования</h2>
    <ul>
      <li>Версия php 8.2</li>
      <li>Редакция bitrix: Старт</li>
    </ul>
  </article>

  <article>
    <h2>Описание</h2>
    <p>Модуль обмена позволяет производить импорт\экспорт из любых в любые сущности.</p>
    <p>
      За основу механизма обмена был взят модуль <a href="https://github.com/sholokhov-daniil/exchange" target="_blank">sholokhov/exchange</a>
    </p>
  </article>

  <article>
    <h2>Карта импорта</h2>
    <p>
      Более подробная документация описана в модуле <a href="https://github.com/sholokhov-daniil/exchange" target="_blank">sholokhov/exchange</a>
    </p>
  </article>

  <article>
    <h2>Запуск обмена</h2>
    <CodeBlock :code="Started"/>
  </article>

</template>