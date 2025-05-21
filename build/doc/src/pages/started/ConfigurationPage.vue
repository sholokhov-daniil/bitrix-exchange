<script setup>
import {reactive} from "vue";
import MainContainer from "@/components/container/MainContainer.vue";
import CodeBlock from "@/components/block-code/CodeBlock.vue";
import {deactivate, setResult} from '@/data/codes/php/started';
import TableContents from "@/components/table-contents/TableContents.vue";

const data = reactive({
  tableContents: [
    {
      title: 'Введение',
      hash: 'vvedenie',
    },
    {
      title: 'Стандартная конфигурация',
      hash: 'standart-config',
      children: [
        {
          title: 'Включение деактивации',
          hash: 'on-deactivate'
        },
        {
          title: 'Результат обмена',
          hash: 'set-result'
        }
      ]
    }
  ]
});
</script>

<template>
  <main-container>
    <h2>Конфигурирование</h2>
    <table-contents :items="data.tableContents" />

  </main-container>

  <main-container>
    <template #header>
      <h2 id="vvedenie">Введение</h2>
    </template>
    <p>
      Все основные конфигурации обмена указываются в его конструктор.

      <br>
      Конфигурация представляет собой ассоциативный массив. Конфигурация обмена является защищенной на чтение и изменение из вне.
      После передачи конфигурации в конструктор обмена мы больше не сможем их скорректировать и прочитать.
      Могут встречаться частные случае, когда пользователю предоставляется возможность переопределить конфигурацию

      <br>
      Конфигурация позволяет настроить поведение вашего обмена и указать куда производится обмен.
      Если указать некорректную конфигурацию, то при инициализации обмена мы можем получить исключение - стоит учесть данный момент.
    </p>
  </main-container>

  <main-container>
    <template #header>
      <h2 id="standart-config">Стандартная конфигурация</h2>
    </template>
    <p>
      Все стандартные обмены являются наследниками класса <a href="./api/classes/Sholokhov-BitrixExchange-Exchange.html" target="_blank">Exchange</a>, который позволяет настроить:
    </p>
    <ul>
      <li>Включение деактивации</li>
      <li>Результат обмена</li>
    </ul>

    <p>
      Каждый отдельно взятый обмен содержит свои доступные параметры и значения. Все доступные обмены описаны в блоке <router-link :to="{name: 'import'}">Импорт</router-link>
    </p>
  </main-container>
  <main-container>
    <h3 id="on-deactivate">Включение деактивации</h3>
    <p>
      После обмена данных производит деактивацию всех значений, которые не приняли участие.
      <br>
      По умолчанию деактивацию отключена. Логика деактивации регламентируется наследником, и она не является обязательной.
      Если наследник откажется реализовывать данный функционал, то данный флаг не повлияет на результат обмена.
    </p>

    <CodeBlock :code="deactivate" />
  </main-container>

  <main-container>
    <template #header>
      <h2 id="set-result">Результат обмена</h2>
    </template>
    <p>
      Каждый обмен обязан вернуть объект результата выполнения, который реализует интерфейс <a href="./api/classes/Sholokhov-BitrixExchange-Messages-ExchangeResultInterface.html" target="_blank">ExchangeResultInterface</a>
      <br>
      Результат содержит все ошибки, которые возникли и все идентификаторы значений, которые принимали участие в импорте\экспорте.
      В угоду оптимизации по умолчанию результат не хранит идентификаторы значений, и для этого нам необходимо сконфигурировать обмен.
      <br>
    </p>

    <code-block :code="setResult" />

    <p>
      Метод <strong>setResultRepository</strong> принимает значение, которое является <a href="https://www.php.net/manual/en/language.types.callable.php" target="_blank">callable</a>,
      и вернет новый объект хранилища реализующий интерфейс <a href="./api/classes/Sholokhov-BitrixExchange-Repository-Result-ResultRepositoryInterface.html" target="_blank">ResultRepositoryInterface</a>.
    </p>
  </main-container>
</template>