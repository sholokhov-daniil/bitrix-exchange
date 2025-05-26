<template>
  <section class="card mt-5 mb-5">
    <div class="card-body">
      <slot name="header"></slot>
      <div class="card-text">
        <slot></slot>
      </div>
    </div>
  </section>
</template>