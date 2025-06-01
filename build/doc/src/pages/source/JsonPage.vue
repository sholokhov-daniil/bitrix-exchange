<script setup>
import MainContainer from "@/components/container/MainContainer.vue";
import ApiLink from "@/components/link/ApiLink.vue";
import CardContainer from "@/components/container/CardContainer.vue";
import CodeBlock from "@/components/block-code/CodeBlock.vue";
import {created, json, result} from "@/data/codes/php/source/json";
import TableBlock from "@/components/table/TableBlock.vue";
</script>

<template>
  <main-container>
    <h1>Источник данных Json</h1>
    <p>
      Класс: <api-link path="classes/Sholokhov-Exchange-Source-Json.html">Json</api-link>
    </p>
  </main-container>

  <card-container>
    <h2>Введение</h2>
    <p>
      Источник данных предназначен, для преобразования данных из JSON в массив и передачу механизму обмена.
      <br>
      Хранение всех данных источника производится в памяти устройства. Перед использованием данного источника стоит учитывать возможные размера данных и ресурсы сервера.
    </p>
  </card-container>

  <card-container>
    <h2>Конфигурация</h2>

    <p>
      Предоставляется возможность указать корневую точку необходимых данных, для этого необходимо при инициализации указать необходимый путь.
      Если путь имеет вложенность, то уровни разделяются символом "."
      <br>
      Рассмотрим пример получения данных из json
    </p>


    <p>
      В качестве первого параметра конструктора определяется json строка, из которой необходимо получить данные, вторым параметром указываются опциональные настройки.
    </p>

    <table-block>
      <template #head>
        <tr>
          <td>Название</td>
          <td>Тип данных</td>
          <td>Значение по умолчанию</td>
          <td>Описание</td>
        </tr>
      </template>

      <tr>
        <td>source_key</td>
        <td>string</td>
        <td></td>
        <td>Путь до размещения данных</td>
      </tr>

      <tr>
        <td>multiple</td>
        <td>boolean</td>
        <td>false</td>
        <td>Значение является итерируемым и его необходимо "перебрать"</td>
      </tr>
    </table-block>


    <p>Формат данных</p>
    <code-block :code="json" />

    <p>Создание источника</p>
    <code-block :code="created" />

    <p>Результат</p>
    <code-block :code="result" />
  </card-container>
</template>