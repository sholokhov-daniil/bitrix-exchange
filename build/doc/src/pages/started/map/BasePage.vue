<script setup>
import {reactive} from "vue";
import MainContainer from "@/components/container/MainContainer.vue";
import ApiLink from "@/components/link/ApiLink.vue";
import {
  codeChildrenLvl,
  codeChildren,
  codeNormalize,
  codePreparation,
  mapBase,
  codeChildrenFirst
} from "@/data/codes/php/started/map";
import CodeBlock from "@/components/block-code/CodeBlock.vue";
import AlertMessage from "@/components/messages/AlertMessage.vue";
import TableContents from "@/components/table-contents/TableContents.vue";
import TableBlock from "@/components/table/TableBlock.vue";
import CardContainer from "@/components/container/CardContainer.vue";

const data = reactive({
  tableContents: [
    {
      title: 'Введение',
      hash: 'start'
    },
    {
      title: 'Структура',
      hash: 'structure',
    },
    {
      title: 'Нормализация',
      hash: 'normalize',
    },
    {
      title: 'Преобразователь',
      hash: 'preparation',
    },
    {
      title: 'Вложенность',
      hash: 'depth',
    }
  ]
});
</script>

<template>
  <main-container>
    <h1>Маршрутизация обмена</h1>

    <table-contents :items="data.tableContents" />
  </main-container>

  <card-container>
    <h2 id="start">Введение</h2>
    <p>
      Карта обмена является неотъемлемый частью в успешном обмене.
      Карта позволяет связать данные из <router-link :to="{name: 'source'}">источника</router-link> со <router-link :to="{name: 'import'}">сущностью</router-link> в которую вставляются данные источника.
    </p>

    <p>
      Каждый класс описывающий свойство принимающее участие в обмене, должен реализовывать интерфейс
      <api-link path="classes/Sholokhov-BitrixExchange-Fields-FieldInterface.html">FieldInterface</api-link>
      <br>
      Разберем пример, который продемонстрирует легкость настройки.
    </p>

    <code-block :code="mapBase" />

    <p>
      Из примера можно заметить, что метод <b>setFrom</b> отвечает за данные из источника данных, а метод <b>setIn</b> за свойства, которые относятся к сущности в которую производим импорт.
      <br>
      Именно данная связь является основополагающей, для организации корректного обмена. Можем заметить, что в карте мы пытаемся получить изображения,
      но не у всех импортируемых элементов оно есть - тут нет нечего страшного, ведь он просто будет иметь пустое значение, и не вызовет исключение или иного отклонения в работе обмена.
      <br>
      Если вы внимательный читатель, то могли обратить внимание на сеттер <b>setPrimary</b> - указывает, что данное поле является ключевым на основе которого будет происходит поиск других элементов, и приниматься решение необходимости добавления или обновления.
    </p>
  </card-container>

  <card-container>
    <h2 id="structure">Структура</h2>
    <p>Класс: <api-link path="classes/Sholokhov-BitrixExchange-Fields-Field.html">Field</api-link></p>

    <p>
      Класс описания имеет стандартный набор методов, позволяющий настроить взаимодействие между источником данных и сущностью в которую производится запись.
      Все стандартные классы описывающие свойство являются наследниками данного объекта.
    </p>
    <table-block>

      <tr>
        <td>Наименование</td>
        <td>Обязательное</td>
        <td>Тип данных</td>
        <td>Описание</td>
      </tr>

      <tr>
        <td>setPrimary</td>
        <td><b>Да</b></td>
        <td>boolean</td>
        <td>Выступает в качестве идентификационного поля(связывает элементы сущности и элементы источника данных)</td>
      </tr>
      <tr>
        <td>setFrom</td>
        <td><b>Да</b></td>
        <td>string</td>
        <td>Путь до значения, которое вернул источник. Если путь является вложенным, то каждый уровень разделяется символом "."</td>
      </tr>
      <tr>
        <td>setIn</td>
        <td><b>Да</b></td>
        <td>string</td>
        <td>В какой ключ будет записываться значение из структуры данных источника</td>
      </tr>
      <tr>
        <td>setPreparation</td>
        <td>Нет</td>
        <td><a href="https://www.php.net/manual/ru/language.types.callable.php" target="_blank">callable</a></td>
        <td>Пользовательский преобразователь значения текущего свойства</td>
      </tr>
      <tr>
        <td>setChildren</td>
        <td>Нет</td>
        <td><api-link path="classes/Sholokhov-BitrixExchange-Fields-FieldInterface.html">FieldInterface</api-link></td>
        <td>Указывает путь до вложенного значения. Предназначен, для получения значения внутри итерируемых элементов</td>
      </tr>
      <tr>
        <td>setNormalizer</td>
        <td>Нет</td>
        <td><a href="https://www.php.net/manual/ru/language.types.callable.php" target="_blank">callable</a></td>
        <td>
          Указываем произвольный нормализатор значения - используется перед преобразованием значения на основе настроек сущности.
          Данный метод может подойти, если нам из источника приходят "грязные" данные.
          Пример: из источника приходит строка "Иванов|34 года", а нам для импорта нужен только "Иванов".
          Наш нормализатор произведет форматирование значения к нужному формату
        </td>
      </tr>
    </table-block>

    <alert-message>
      <template #header>
        Внимание!
      </template>
      <p>В карте должно быть только одно свойство, которое выступает идентификационным <b>(setPrimary)</b></p>
    </alert-message>
  </card-container>

  <card-container>
    <h2 id="normalize">Нормализация значения</h2>
    <p>
      Рассмотрим пример использования пользовательского обработчика значений, в поле "user" хранятся значения формата <b>{фамилия}|{возраст}</b>.
      В рамках данной задачи нам необходимо из этой строки получить фамилию.
      <br>
      Наш нормализатор получает 2 входных параметра:
    </p>
    <ul>
       <li>Значение, которое необходимо нормализовать (может иметь любой тип данных)</li>
       <li>Свойство, значение которого нормализуем. Оно имеет тип данных <api-link path="classes/Sholokhov-BitrixExchange-Fields-FieldInterface.html">FieldInterface</api-link></li>
    </ul>

    <code-block :code="codeNormalize" />
  </card-container>

  <card-container>
    <h2 id="preparation">Преобразователь значения</h2>
    <p>
      Каждое свойство наделено возможностью указания собственного преобразователя данных, если значение необходимо обработать уникальным подходом и игнорировать альтернативные решения.
      Указав свойству собственный нормализатор, в таком случае все зарегистрированные нормализаторы внутри обмена игнорируются.
      <br>
      Результатом работы нормализатора служит объект реализующий интерфейс <api-link path="classes/Sholokhov-BitrixExchange-Messages-DataResultInterface.html">DataResultInterface</api-link>
    </p>
    <h4>Пример</h4>
    <p>
      Перед нами стоит цель: создать собственный преобразователь значения типа файл.
      В качестве значения нам передается символьный код, который позволяет получить изображение из внешнего ресурса.
      <br>
      Может показаться, что можно использовать нормализатор данных, но нормализатор предназначен, для форматирования значения.
      В нашем примере помимо преобразования пути предстоит произвести http запрос и скачать изображение, предварительно сохранив в файловой системе сервера.
      Согласитесь, довольно много обязанностей, для обычного форматирования.
    </p>

    <code-block :code="codePreparation" />
  </card-container>

  <card-container>
    <h2 id="depth">Вложенный путь</h2>
    <p>
      В некоторых случаях нам может потребоваться получить значения внутри итерируемого объекта.
      Рассмотрим пример на массиве, где нужно получить путь до изображения
    </p>

    <code-block :code="codeChildren" />

    <p>
      Разберем более подробно пример приведенный ранее.
      <br>
      Для этого нам необходимо понять как происходит парсинг значения.
      У парсера зарезервирован символ ".", который отвечает за разделение уровней данных.
      Если в пути используется символ ".", то это означает, что теперь производим поиск ключа внутри данного значения: <b>parentKey.childrenKey</b>.
    </p>
    <p>
      Из этого следует, что нам необходимо явно указывать путь до нашего значения, соблюдая все ключи массива.
      В примере мы дошли до массива изображений, и тут встала задача получения значения ключа "sdn" у каждого изображения.
      <br>
      Метод <b>setChildren</b> сообщает парсеру, что нужно зайти в каждый элемент массива и достать из него значение.
      Вложенные пути могут быть более сложными - допускается возможность указать N вложенность
    </p>
    <code-block :code="codeChildrenLvl" />

    <p>
      Нам может потребоваться возможность получения только определенного значения внутри итерации.
      <br>
      Разберем пример, что перед нами стоит задача: получить путь только до первого изображения
    </p>

    <code-block :code="codeChildrenFirst" />
  </card-container>
</template>