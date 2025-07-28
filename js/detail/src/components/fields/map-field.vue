<script setup>
import {Select, GridRow, CheckBoxб Input} from 'ui';
</script>

<template>
  <GridRow>
    <template #title>Тип поля</template>
    <template #content>
      <Select name="type"></Select>
    </template>
  </GridRow>

  <GridRow>
    <template #title>Ключевое поле</template>
    <template #content>
      <CheckBox name="primary" />
    </template>
  </GridRow>

  <GridRow>
    <template #title>Путь до значения</template>
    <template #content>
      <Input name="from" />
    </template>
  </GridRow>

  <GridRow>
    <template #title>Код свойства в которое необходимо записать значение. Тут нужно подумать как это сделать</template>
    <template #content>
      <Input name="to" />
    </template>
  </GridRow>

  <GridRow>
    <template #title>Хранит хеш обмена</template>
    <template #content>
      <CheckBox name="hash" />
    </template>
  </GridRow>

  <GridRow>
    <template #title>При отсутствии связующей сущности произвести его создание</template>
    <template #content>
      <CheckBox name="created_link" />
    </template>
  </GridRow>

  <GridRow>
    <template #title>Дочерние элементы</template>
    <template #content>
      Тут нужно будет делать связь
    </template>
  </GridRow>
</template>