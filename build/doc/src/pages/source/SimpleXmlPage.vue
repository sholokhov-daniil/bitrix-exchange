<script setup>
import MainContainer from "@/components/container/MainContainer.vue";
import ApiLink from "@/components/link/ApiLink.vue";
import CardContainer from "@/components/container/CardContainer.vue";
import CodeBlock from "@/components/block-code/CodeBlock.vue";
import {example, result, rootTag, xml} from "@/data/codes/php/source/simple-xml";
</script>

<template>
  <main-container>
    <h1>Источник данных из XML</h1>
    <p>
      Класс: <api-link path="classes/Sholokhov-Exchange-Source-SimpleXml.html">SimpleXml</api-link>
    </p>
  </main-container>

  <card-container>
    <h2>Введение</h2>
    <p>
      Источник данных предназначен, для преобразования из XML в массив и передаче обмену.
      <br>
      Текущий источник все данные хранит в памяти и подходит для не больших данных, в ином случае необходимо использовать <router-link :to="{name: 'source-xml'}">источник</router-link>.
    </p>
    <p>
      Источник данных использует сериализатор <a href="https://github.com/symfony/serializer-pack/releases/tag/v1.3.0" target="_blank">symfony serializer</a>
    </p>
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
  </card-container>
</template>