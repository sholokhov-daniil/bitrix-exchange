<script setup>
import {defineProps, computed} from 'vue';

const props = defineProps({
  item: {type: Array, default: () => []}
});

const target = computed(() => `${props.item.code}-collapse`);
const isParent = computed(() => props.item.children && props.item.children.length);
</script>

<template>
  <li v-if="isParent" class="mb-1">
    <button class="btn btn-toggle align-items-center rounded collapsed" data-bs-toggle="collapse" :data-bs-target="`#${target}`" aria-expanded="true">
      {{ item.title }}
    </button>

    <div class="collapse show" :id="target">
      <ul class="btn-toggle-nav list-unstyled fw-normal pb-1 small">
        <node-tree v-for="children in item.children" :key="children.title" :item="children" />
      </ul>
    </div>
  </li>
  <li v-else>
    <a v-if="item.link" :href="item.link" target="_blank" class="link-dark rounded btn align-items-center rounded collapsed font-weight-bold">{{item.title}}</a>
    <router-link
        v-else-if="item.code"
        :to="{name: item.code}"
        active-class="router-link-active"
        class="link-dark rounded"
        style="text-decoration:none"
    >
      {{ item.title }}
    </router-link>
    <span v-else class="link-dark rounded">{{ item.title }}</span>
  </li>
</template>

<style scoped>
main {
  display: flex;
  flex-wrap: nowrap;
  height: 100vh;
  height: -webkit-fill-available;
  max-height: 100vh;
  overflow-x: auto;
  overflow-y: hidden;
}

.b-example-divider {
  flex-shrink: 0;
  width: 1.5rem;
  height: 100vh;
  background-color: rgba(0, 0, 0, .1);
  border: solid rgba(0, 0, 0, .15);
  border-width: 1px 0;
  box-shadow: inset 0 .5em 1.5em rgba(0, 0, 0, .1), inset 0 .125em .5em rgba(0, 0, 0, .15);
}

.bi {
  vertical-align: -.125em;
  pointer-events: none;
  fill: currentColor;
}

.dropdown-toggle { outline: 0; }

.nav-flush .nav-link {
  border-radius: 0;
}

.btn-toggle {
  display: inline-flex;
  align-items: center;
  padding: .25rem .5rem;
  font-weight: 600;
  color: rgba(0, 0, 0, .65);
  background-color: transparent;
  border: 0;
}
.btn-toggle:hover,
.router-link-active {
  color: rgba(0, 0, 0, .85);
  background-color: #d2f4ea;
}

.btn-toggle::before {
  width: 1.25em;
  line-height: 0;
  content: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' viewBox='0 0 16 16'%3e%3cpath fill='none' stroke='rgba%280,0,0,.5%29' stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M5 14l6-6-6-6'/%3e%3c/svg%3e");
  transition: transform .35s ease;
  transform-origin: .5em 50%;
}

.btn-toggle[aria-expanded="true"] {
  color: rgba(0, 0, 0, .85);
}
.btn-toggle[aria-expanded="true"]::before {
  transform: rotate(90deg);
}

.btn-toggle-nav>li {
  padding: .1875rem .5rem;
  margin-top: .125rem;
  margin-left: 1.25rem;
}
.btn-toggle-nav a {
  display: inline-flex;
  text-decoration: none;
}

.btn-toggle-nav a:hover,
.btn-toggle-nav a:focus {
  background-color: #d2f4ea;
}

.scrollarea {
  overflow-y: auto;
}

.fw-semibold { font-weight: 600; }
.lh-tight { line-height: 1.25; }
</style>