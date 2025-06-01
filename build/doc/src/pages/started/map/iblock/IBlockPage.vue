<script setup>
import MainContainer from "@/components/container/MainContainer.vue";
import ApiLink from "@/components/link/ApiLink.vue";

// TODO: Тут нужно поправить ссылки
</script>

<template>
  <main-container>
    <h1>Свойство информационного блока</h1>
    <p>Класс: <api-link path="classes/Sholokhov-Exchange-Fields-IBlock-IBlockElementField.html">IBlockElementField</api-link></p>

    <p>
      Класс является наследником <router-link :to="{name: 'map-base'}">стандартного свойства</router-link> и поведение идентично ему.
    </p>

    <p>
      Текущее свойство предназначено для описания <a href="https://dev.1c-bitrix.ru/learning/course/index.php?COURSE_ID=43&LESSON_ID=3399" target="_blank">пользовательских свойств</a> информационного блока,
      и используется импортом <api-link path="classes/Sholokhov-Exchange-Target-IBlock-Element.html">элементов</api-link> информационного блока.
      <br>
      Если импорт в ходе следующих действий встречает класс, который реализовывает интерфейс <api-link path="classes/Sholokhov-Exchange-Fields-IBlock-ElementFieldInterface.html">ElementFieldInterface</api-link>, то работает как со свойством:
    </p>
    <ul>
      <li>Преобразование значения</li>
      <li>Запись значений свойств</li>
    </ul>
  </main-container>
</template>