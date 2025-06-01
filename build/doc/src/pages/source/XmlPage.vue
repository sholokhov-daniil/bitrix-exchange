<script setup>
import MainContainer from "@/components/container/MainContainer.vue";
import ApiLink from "@/components/link/ApiLink.vue";
import CardContainer from "@/components/container/CardContainer.vue";
import CodeBlock from "@/components/block-code/CodeBlock.vue";
import {
  depthPractices,
  example,
  result,
  resultDepth,
  rootTag,
  rootTagDepth,
  xml,
  xmlDepth
} from "@/data/codes/php/source/xml";
import AlertMessage from "@/components/messages/AlertMessage.vue";
</script>

<template>
  <main-container>
    <h1>Источник данных из XML</h1>
    <p>
      Класс: <api-link path="classes/classes/Sholokhov-Exchange-Source-Xml.html">Xml</api-link>
    </p>
  </main-container>

  <card-container>
    <h2>Введение</h2>
    <p>
      Источник данных предназначен, для преобразования из XML в массив и передаче обмену.
      <br>
      Текущий источник данных является более медленным по сравнению с <router-link :to="{name: 'source-simple-xml'}">SimpleXml</router-link>,
      но может работать с любым объемом данных.
      <br>
      Чтение XML файла идет по строкам, а результат чтения записывается таблицы, уже после этого идет взаимодействия с базой данных,
      и не приходится держать в памяти весь файл.
      <br>
      Для хранения данных создается уникальная таблица, а по окончанию работы удаляется,
      если произошло экстренное прерывание выполнения php скрипта, то библиотека использует агентов, которые производят мониторинг динамических таблиц.
    </p>
    <p>
      Источник данных использует стандартный парсер битрикса <a href="https://dev.1c-bitrix.ru/api_help/iblock/classes/ciblockxmlfile/index.php" target="_blank">CIBlockXMLFile</a>
    </p>

    <alert-message>
      <template #header>
        Внимание
      </template>

      Для работы необходимо наличие установленного штатного модуля iblock
    </alert-message>
  </card-container>

  <card-container>
    <h2>Конфигурация</h2>

    <p>
      При инициализации объекта в конструктор мы обязаны указать путь до xml файла.
      Разберем пример инициализации источника данных
    </p>
    <code-block :code="example" />

    <p>
      Может потребоваться получить данные из определенной точки xml, для этого необходимо указать путь через <b>setRootTag</b>
      <br>
      Рассмотрим пример получения значения из атрибута item
    </p>

    <p>Формат данных</p>
    <code-block :code="xml" />

    <p>Создание источника</p>
    <code-block :code="rootTag" />

    <p>Результат</p>
    <code-block :code="result" />

    <p>
      Текущий источник данных имеет собственный синтаксис указания пути до размещения корня данных.
      В большинстве случаев вложенный путь указывается через символ ".", но в данном случае указываем нужный тег и уровень вложенности.
      <br>
      Рассмотрим пример получения данных из более глубокого размещения
    </p>

    <p>Формат данных</p>
    <code-block :code="xmlDepth" />

    <p>Создание источника</p>
    <code-block :code="rootTagDepth" />

    <p>Результат</p>
    <code-block :code="resultDepth" />

    <p>
      Можно было заметить, что мы использовали метод <b>setRootTagDepth</b>.
      Данный метод предназначен, для указания коревого уровня вложенности, чтобы избежать ошибок определения.
      <br>
      Парсер битрикса не учитывает вложенность и подразумевает, что каждый тег уникальный, и не может размещаться в разных местах XML файла.
      Если мы не укажем уровень вложенности, то будет производиться глобальный поиск по всему XML файлу
    </p>
    <code-block :code="depthPractices" />


  </card-container>
</template>