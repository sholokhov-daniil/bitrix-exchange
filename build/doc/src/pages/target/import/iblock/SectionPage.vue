<script setup>
import ApiLink from "@/components/link/ApiLink.vue";
import AlertMessage from "@/components/messages/AlertMessage.vue";
import CodeBlock from "@/components/block-code/CodeBlock.vue";
import {
  afterAdd,
  afterUpdate, errorBeforeAdd,
  beforeDeactivate,
  errorBeforeUpdate,
  exampleStart,
  medifBeforeUpdateCancel, modifBeforeAdd, modifBeforeAddCancel,
  modifBefureUpdate
} from "@/data/codes/php/target/import/iblock/section";
import CardContainer from "@/components/container/CardContainer.vue";
import {reactive} from "vue";
import TableContents from "@/components/table-contents/TableContents.vue";

const data = reactive({
  tableContents: [
    {
      title: 'Конфигурация',
      hash: 'configuration',
    },
    {
      title: 'Пример',
      hash: 'example',
    },
    {
      title: 'События',
      hash: 'events',
      children: [
        {
          title: 'onBeforeIBlockElementUpdate',
          hash: 'before-update',
        },
        {
          title: 'onAfterIBlockElementUpdate',
          hash: 'after-update'
        },
        {
          title: 'onBeforeIBlockElementAdd',
          hash: 'before-add'
        },
        {
          title: 'onAfterIBlockElementAdd',
          hash: 'after-add'
        },
        {
          title: 'onBeforeIBlockElementsDeactivate',
          hash: 'before-deactivate',
        }
      ]
    }
  ]
})
</script>

<template>
  <card-container>
    <template #header>
      <h1>Импорт разделов инфоблока</h1>
    </template>

    <p>
      Класс: <api-link path="classes/Sholokhov-BitrixExchange-Target-IBlock-Section.html">Section</api-link>
      <br>
      Наследник класса: <api-link path="classes/Sholokhov-BitrixExchange-Exchange.html">Exchange</api-link>
    </p>

    <table-contents :items="data.tableContents" />
  </card-container>

  <card-container>
    <template #header>
      <h2 id="configuration">Конфигурация</h2>
    </template>

    <p>
      Импорт поддерживает следующий формат конфигурации (иные ключи пропускаются и не используются):
    </p>

    <table class="table table-dark table-striped">
      <thead>
      <tr>
        <td>Название</td>
        <td>Обязательное</td>
        <td>Тип данных</td>
        <td>Значение по умолчанию</td>
        <td>Описание</td>
      </tr>
      </thead>
      <tr>
        <td>iblock_id</td>
        <td>Да</td>
        <td>int</td>
        <td>Нет</td>
        <td>Идентификатор информационного блока в который производится импорт</td>
      </tr>
    </table>
  </card-container>

  <card-container>
    <template #header>
      <h2 id="example">Пример</h2>
    </template>

    <p>
      Разберем пример запуска импорта элементов информационного блока
    </p>

    <code-block :code="exampleStart" />
  </card-container>

  <card-container>
    <template #header>
      <h2 id="events">События</h2>
    </template>

    <p>
      Импорт в ходе своей работы вызывает события, которые могут помочь произвести его модификацию и проконтролировать процесс выполнения импорта разделов.
      <br>
      Все события реализованы на <a href="https://dev.1c-bitrix.ru/learning/course/index.php?COURSE_ID=43&LESSON_ID=3113" target="_blank">d7</a>
      Рассмотрим список доступных событий
    </p>
  </card-container>

  <card-container>
    <template #header>
      <h3 id="before-update">onBeforeIBlockSectionUpdate</h3>
    </template>

    <p>
      Вызывается перед обновлением раздела.
      <br>
      Передаваемые параметры в обработчик
    </p>

    <table class="table table-dark table-striped">
      <thead>
      <tr>
        <td>Название</td>
        <td>Обязательное</td>
        <td>Тип данных</td>
        <td>Передается по ссылке</td>
        <td>Описание</td>
      </tr>
      </thead>
      <tr>
        <td>fields</td>
        <td>Да</td>
        <td>array</td>
        <td>Да</td>
        <td>Подготовленные данные, которые будут принимать участие в обновлении.</td>
      </tr>
      <tr>
        <td>id</td>
        <td>Да</td>
        <td>int</td>
        <td>Нет</td>
        <td>Идентификатор элемента, который было обновляться</td>
      </tr>
    </table>

    <alert-message>
      <template #header>Внимание</template>

      Присутствует возможность отменить создание раздела, для этого необходимо вызвать исключение <api-link path="classes/Sholokhov-BitrixExchange-Exception-Target-ExchangeItemStoppedException.html">ExchangeItemStoppedException</api-link>
      После отмены создания производится запись в лог файл с возможностью переопределения сообщения. В качестве сообщения, для записи в лог берется сообщение исключения.
    </alert-message>

    <alert-message>
      <template #header>Внимание</template>

      Если событие вернет статут отличный от успешного, то будет считаться, что в пользовательском событии возникла ошибка, и добавление не произойдет.
      <br>
      Присутствует возможность передачи массив ошибок, которые будут перенесены в результат работы импорта.
    </alert-message>

    <h4>Пример модификации данных</h4>
    <code-block :code="modifBefureUpdate" />

    <h4>Пример отмены обновления</h4>
    <code-block :code="medifBeforeUpdateCancel" />

    <h4>Пример передачи ошибки при выполнении события</h4>
    <code-block :code="errorBeforeUpdate" />
  </card-container>

  <card-container>
    <template #header>
      <h3 id="after-update">onAfterIBlockSectionUpdate</h3>
    </template>

    <p>
      Вызывается после выполнения обновления раздела.
      Передаваемые параметры в обработчик
    </p>

    <table class="table table-dark table-striped">
      <thead>
      <tr>
        <td>Название</td>
        <td>Обязательное</td>
        <td>Тип данных</td>
        <td>Передается по ссылке</td>
        <td>Описание</td>
      </tr>
      </thead>
      <tr>
        <td>fields</td>
        <td>Да</td>
        <td>array</td>
        <td>Нет</td>
        <td>Подготовленные данные, которые будут принимать участие в обновлении.</td>
      </tr>
      <tr>
        <td>id</td>
        <td>Да</td>
        <td>int</td>
        <td>Нет</td>
        <td>Идентификатор элемента, который будет обновляться</td>
      </tr>
      <tr>
        <td>result</td>
        <td>Да</td>
        <td>
          <api-link path="classes/Sholokhov-BitrixExchange-Messages-DataResultInterface.html">DataResultInterface</api-link>
        </td>
        <td>Нет</td>
        <td>Результат обновления раздела</td>
      </tr>
    </table>

    <p>Событие не позволяет вмешаться в процесс обмена, а только служит флагом, дря разработчика</p>
    <h4>Пример подписки на событие</h4>

    <code-block :code="afterUpdate" />
  </card-container>

  <card-container>
    <template #header>
      <h3 id="before-add">onBeforeIBlockSectionAdd</h3>
    </template>

    <p>
      Вызывается перед созданием раздела.
      <br>
      Передаваемые параметры в обработчик
    </p>

    <table class="table table-dark table-striped">
      <thead>
      <tr>
        <td>Название</td>
        <td>Обязательное</td>
        <td>Тип данных</td>
        <td>Передается по ссылке</td>
        <td>Описание</td>
      </tr>
      </thead>
      <tr>
        <td>fields</td>
        <td>Да</td>
        <td>array</td>
        <td>Да</td>
        <td>Подготовленные данные, которые будут принимать участие в добавлении</td>
      </tr>
    </table>

    <alert-message>
      <template #header>Внимание</template>

      Присутствует возможность отменить добавления раздела, для этого необходимо вызвать исключение <api-link path="classes/Sholokhov-BitrixExchange-Exception-Target-ExchangeItemStoppedException.html">ExchangeItemStoppedException</api-link>
      После отмены добавления производится запись в лог файл с возможностью переопределения сообщения. В качестве сообщения, для записи в лог берется сообщение исключения.
    </alert-message>

    <alert-message>
      <template #header>Внимание</template>

      Если событие вернет статут отличный от успешного, то будет считаться, что в пользовательском событии возникла ошибка, и добавление не произойдет.
      <br>
      Присутствует возможность передачи массив ошибок, которые будут перенесены в результат работы импорта.
    </alert-message>

    <h4>Пример модификации данных</h4>
    <code-block :code="modifBeforeAdd" />

    <h4>Пример отмены</h4>
    <code-block :code="modifBeforeAddCancel" />

    <h4>Пример передачи ошибки при выполнении события</h4>
    <code-block :code="errorBeforeAdd" />
  </card-container>

  <card-container>
    <template #header>
      <h3 id="after-add">onAfterIBlockSectionAdd</h3>
    </template>

    <p>
      Вызывается после выполнения добавления раздела.
      Передаваемые параметры в обработчик
    </p>

    <table class="table table-dark table-striped">
      <thead>
      <tr>
        <td>Название</td>
        <td>Обязательное</td>
        <td>Тип данных</td>
        <td>Передается по ссылке</td>
        <td>Описание</td>
      </tr>
      </thead>
      <tr>
        <td>fields</td>
        <td>Да</td>
        <td>array</td>
        <td>Да</td>
        <td>Подготовленные данные, которые будут принимать участие в добавлении</td>
      </tr>
      <tr>
        <td>id</td>
        <td>Да</td>
        <td>int</td>
        <td>Нет</td>
        <td>Идентификатор элемента, который было добавлено</td>
      </tr>
      <tr>
        <td>result</td>
        <td>Да</td>
        <td>
          <api-link path="classes/Sholokhov-BitrixExchange-Messages-DataResultInterface.html">DataResultInterface</api-link>
        </td>
        <td>Нет</td>
        <td>Результат добавления</td>
      </tr>
    </table>

    <p>Событие не позволяет вмешаться в процесс обмена, а только служит флагом, дря разработчика</p>
    <h4>Пример подписки на событие</h4>

    <code-block :code="afterAdd" />
  </card-container>

  <card-container>
    <template #header>
      <h3 id="before-deactivate">onBeforeIBlockSectionsDeactivate</h3>
    </template>

    <p>
      Вызывается перед деактивацией разделов, которые не пришли в импорте
      <br>
      Передаваемые параметры в обработчик
    </p>

    <table class="table table-dark table-striped">
      <thead>
      <tr>
        <td>Название</td>
        <td>Обязательное</td>
        <td>Тип данных</td>
        <td>Передается по ссылке</td>
        <td>Описание</td>
      </tr>
      </thead>
      <tr>
        <td>parameters</td>
        <td>Да</td>
        <td>array</td>
        <td>Да</td>
        <td>Параметры запроса <a href="https://dev.1c-bitrix.ru/api_d7/bitrix/iblock/elementtable/index.php" target="_blank">ElementTable::getList</a></td>
      </tr>
    </table>

    <p>Событие не позволяет вмешаться в процесс деактивации, а только служит флагом, дря разработчика</p>
    <h4>Пример подписки на событие</h4>

    <code-block :code="beforeDeactivate" />
  </card-container>

</template>