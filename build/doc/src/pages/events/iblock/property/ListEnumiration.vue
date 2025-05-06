<script setup>
import CodeBlock from "@/components/block-code/CodeBlock.vue";
import {BeforeAdd, CancelBeforeAdd, AfterAdd, BeforeUpdate, CancelBeforeUpdate, AfterUpdate} from "@/data/codes/php/events/iblock/properties/enumiration";
</script>

<template>
  <article>
    <h1>События импорта значений списка в свойство информационного блока</h1>
    <p>Класс <a href="/api/classes/Sholokhov-BitrixExchange-Target-IBlock-Property-PropertyEnumeration.html" target="_blank">PropertyEnumeration</a> </p>

    <h2>События</h2>
    <ul>
      <li><a href="#onbeforeiblockpropertyenumerationadd">onBeforeIBlockPropertyEnumerationAdd</a></li>
      <li><a href="#onafteriblockpropertyenumerationadd">onAfterIBlockPropertyEnumerationAdd</a></li>
      <li><a href="#onbeforeiblockpropertyenumerationupdate">onBeforeIBlockPropertyEnumerationUpdate</a></li>
      <li><a href="#onafteriblockpropertyenumerationupdate">onAfterIBlockPropertyEnumerationUpdate</a></li>
    </ul>
  </article>

  <article class="card mt-5 mb-5">
    <div class="card-body">
      <h2 class="card-title" id="onbeforeiblockpropertyenumerationadd">onBeforeIBlockPropertyEnumerationAdd</h2>
      <div class="card-text">
        <p>Событие вызывается перед созданием значения списка</p>

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
  </article>

  <article class="card mt-5 mb-5">
    <div class="card-body">
      <h2 class="card-title" id="onafteriblockpropertyenumerationadd">onAfterIBlockPropertyEnumerationAdd</h2>
      <div class="card-text">
        <p>Событие вызывается после добавления значения в список</p>

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
  </article>

  <article class="card mt-5 mb-5">
    <div class="card-body">
      <h2 class="card-title" id="onbeforeiblockpropertyenumerationupdate">onBeforeIBlockPropertyEnumerationUpdate</h2>
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
          <p>Присутствует возможность отмены изменения значения.</p>
          <p>Если отменить изменение, то в лог файле появится соответствующее сообщение, но в результате работы импорта это не отобразится.</p>
        </div>

        <h3 class="pt-2">Пример отмены добавления</h3>
        <code-block :code="CancelBeforeUpdate" />
      </div>
    </div>
  </article>

  <article class="card mt-5 mb-5">
    <div class="card-body">
      <h2 class="card-title" id="onafteriblockpropertyenumerationupdate">onAfterIBlockPropertyEnumerationUpdate</h2>
      <div class="card-text">
        <p>Событие вызывается после обновления значения списка</p>

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
  </article>

</template>