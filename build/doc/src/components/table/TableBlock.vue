<template>
  <div class="table-container">
    <table class="table table-bordered">
      <thead>
        <slot name="head"></slot>
      </thead>
      <tbody>
        <slot></slot>
      </tbody>
    </table>
  </div>
</template>

<style scoped>
.table-container {
  overflow: auto;
}
</style>