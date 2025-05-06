<script setup>
import {reactive} from 'vue';
import CodeBlock from "@/components/block-code/CodeBlock.vue";
import {BeforeAdd, CancelBeforeAdd, AfterAdd, BeforeUpdate, CancelBeforeUpdate, AfterUpdate} from "@/data/codes/php/events/iblock/element/import";
import TableContents from "@/components/table-contents/TableContents.vue";

const data = reactive({
  tableContents: [
    {
      title: 'onBeforeIBlockElementAdd',
      hash: 'before-add',
    },
    {
      title: 'onAfterIBlockElementAdd',
      hash: 'after-add',
    },
    {
      title: 'onBeforeIBlockElementUpdate',
      hash: 'before-update',
    },
    {
      title: 'onAfterIBlockElementUpdate',
      hash: 'after-update',
    },
  ]
});
</script>

<template>
  <section>
    <h1>События импорта элементов информационного блока</h1>
    <p>Класс <a href="/api/classes/Sholokhov-BitrixExchange-Target-IBlock-Element.html" target="_blank">Element</a></p>

    <h2 class="pt-2">События</h2>
    <table-contents
      :items="data.tableContents"
    />
  </section>

    <section class="card mt-5 mb-5">
      <div class="card-body">
        <h2 class="card-title" id="before-add">onBeforeIBlockElementAdd</h2>
        <div class="card-text">
          <p>Событие вызывается перед созданием элемента</p>

          <h3 class="pt-2">Параметры события</h3>
          <table class="table table-dark table-striped">
            <thead>
            <tr>
              <td>Название</td>
              <td>Тип данных</td>
              <td>Обязательность</td>
              <td>Примечание</td>
            </tr>
            </thead>
            <tbody>
            <tr>
              <td>FIELDS</td>
              <td>array</td>
              <td>Да</td>
              <td>Значение передается по ссылке</td>
            </tr>
            </tbody>
          </table>

          <h3 class="pt-2">Пример подписки на событие</h3>
          <code-block :code="BeforeAdd" />

          <div class="alert alert-info mt-2" role="alert">
            <p>Присутствует возможность отмены добавления значения.</p>
            <p>Если отменить добавление, то в лог файле появится соответствующее сообщение, но в результате работы импорта это не отобразится.</p>
          </div>

          <h3 class="pt-2">Пример отмены добавления</h3>
          <code-block :code="CancelBeforeAdd" />
        </div>
      </div>
    </section>

    <section class="card mt-5 mb-5">
      <div class="card-body">
        <h2 class="card-title" id="after-add">onAfterIBlockElementAdd</h2>
        <div class="card-text">
          <p>Событие вызывается после добавления элемента</p>

          <h3 class="pt-2">Параметры события</h3>
          <table class="table table-dark table-striped">
            <thead>
            <tr>
              <td>Название</td>
              <td>Тип данных</td>
              <td>Обязательность</td>
              <td>Примечание</td>
            </tr>
            </thead>
            <tbody>
            <tr>
              <td>ID</td>
              <td>int</td>
              <td>Да</td>
              <td>ID созданного элемента</td>
            </tr>
            <tr>
              <td>FIELDS</td>
              <td>array</td>
              <td>Да</td>
              <td>Массив с добавляемыми значениями</td>
            </tr>
            <tr>
              <td>RESULT</td>
              <td>Sholokhov\Exchange\Messages\Type\DataResult</td>
              <td>Да</td>
              <td>Результат создания</td>
            </tr>
            </tbody>
          </table>

          <h3 class="pt-2">Пример подписки на событие</h3>

          <code-block :code="AfterAdd" />
        </div>
      </div>
    </section>

    <section class="card mt-5 mb-5">
      <div class="card-body">
        <h2 class="card-title" id="before-update">onBeforeIBlockElementUpdate</h2>
        <div class="card-text">
          <p>Событие перед изменением элемента</p>

          <h3 class="pt-2">Параметры события</h3>
          <table class="table table-dark table-striped">
            <thead>
            <tr>
              <td>Название</td>
              <td>Тип данных</td>
              <td>Обязательность</td>
              <td>Примечание</td>
            </tr>
            </thead>
            <tbody>
            <tr>
              <td>ID</td>
              <td>int</td>
              <td>Да</td>
              <td>ID обновляемого элемента</td>
            </tr>
            <tr>
              <td>FIELDS</td>
              <td>array</td>
              <td>Да</td>
              <td>Значение передаются по ссылке</td>
            </tr>
            </tbody>
          </table>

          <h3 class="pt-2">Пример подписки на событие</h3>

          <code-block :code="BeforeUpdate" />

          <div class="alert alert-info mt-2" role="alert">
            <p>Присутствует возможность отмены изменения элемента.</p>
            <p>Если отменить изменение, то в лог файле появится соответствующее сообщение, но в результате работы импорта это не отобразится.</p>
          </div>

          <h3 class="pt-2">Пример отмены добавления</h3>
          <code-block :code="CancelBeforeUpdate" />
        </div>
      </div>
    </section>

    <section class="card mt-5 mb-5">
      <div class="card-body">
        <h2 class="card-title" id="after-update">onAfterIBlockElementUpdate</h2>
        <div class="card-text">
          <p>Событие вызывается после обновления элемента сущности и передаются следующие параметры</p>

          <h3 class="pt-2">Параметры события</h3>
          <table class="table table-dark table-striped">
            <thead>
            <tr>
              <td>Название</td>
              <td>Тип данных</td>
              <td>Обязательность</td>
              <td>Примечание</td>
            </tr>
            </thead>
            <tbody>
            <tr>
              <td>ID</td>
              <td>int</td>
              <td>Да</td>
              <td>ID обновляемого элемента</td>
            </tr>
            <tr>
              <td>FIELDS</td>
              <td>array</td>
              <td>Да</td>
              <td>Массив с обновленными значениями</td>
            </tr>
            <tr>
              <td>RESULT</td>
              <td>Sholokhov\Exchange\Messages\Type\DataResult</td>
              <td>Да</td>
              <td>Результат обновления</td>
            </tr>
            </tbody>
          </table>

          <h3 class="pt-2">Пример подписки на событие</h3>

          <code-block :code="AfterUpdate" />
        </div>
      </div>
    </section>
</template>