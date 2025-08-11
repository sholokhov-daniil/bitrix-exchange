<template>
  <RowTable v-bind="$attrs">
    <ColumnTitleTable>
      <slot name="title"></slot>
    </ColumnTitleTable>

    <ColumnValueTable>
      <slot name="content"></slot>
    </ColumnValueTable>
  </RowTable>
</template>

<script setup>
import ColumnTitleTable from "../table/column-title-table.vue";
import ColumnValueTable from "../table/column-value-table.vue";
import RowTable from "../table/row-table.vue";
</script>