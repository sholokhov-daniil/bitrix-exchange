<script setup>
import MainContainer from "@/components/container/MainContainer.vue";
import ApiLink from "@/components/link/ApiLink.vue";
import AlertMessage from "@/components/messages/AlertMessage.vue";
import CodeBlock from "@/components/block-code/CodeBlock.vue";
import {exampleDataFormat} from "@/data/codes/php/target/import/file";
import TableBlock from "@/components/table/TableBlock.vue";
</script>

<template>
  <main-container>
    <h1>Импорт файлов</h1>
    <p>
      Класс: <api-link path="classes/Sholokhov-BitrixExchange-Target-File.html">File</api-link>
      <br>
      Наследник класса: <api-link path="classes/Sholokhov-BitrixExchange-Exchange.html">Exchange</api-link>
    </p>
  </main-container>

  <main-container>
    <h2>Введение</h2>
    <p>
      Импорт файлов предназначен, для загрузки произвольных файлов в файловую систему сервера и регистрации в битриксе.
      <br>
      Уникальность файла определяется по его <a href="https://www.php.net/manual/en/function.hash-file.php" target="_blank">хешу</a> по алгоритму <a href="https://www.php.net/manual/en/function.md5.php" target="_blank">md5</a>
    </p>
    <alert-message>
      <template #header>Внимание</template>
      Импорт не поддерживает массив файлов
    </alert-message>
    <alert-message>
      <template #header>Внимание</template>
      Импорт не производит обновление существующего файла
    </alert-message>

    <p>
      Рассмотрим пример ожидаемого формата данных
    </p>
    <code-block :code="exampleDataFormat" />
  </main-container>

  <main-container>
    <h2>Конфигурация</h2>
    <p>
      Импорт поддерживает следующий формат конфигурации (иные ключи пропускаются и не используются):
    </p>

    <table-block>
      <template #head>
        <tr>
          <td>Название</td>
          <td>Обязательное</td>
          <td>Тип данных</td>
          <td>Значение по умолчанию</td>
          <td>Описание</td>
        </tr>
      </template>

      <tr>
        <td>module_id</td>
        <td>Нет</td>
        <td>string</td>
        <td>iblock</td>
        <td>Модуль, которому относится файл</td>
      </tr>
    </table-block>

  </main-container>
</template>