<script setup>
import LeftMenuSource from '@/data/structure';
import LeftMenuTree from "@/components/menu/left/LeftMenuTree.vue";
</script>

<template>
  <div class="container-fluid">
    <div class="row">
      <div class="col-2">
        <h1><RouterLink to="/">Exchange</RouterLink></h1>
      </div>
    </div>
    <div class="row">
      <div class="col-2">
        <LeftMenuTree :items="LeftMenuSource()"/>
      </div>
      <div class="col">
        <transition>
          <router-view />
        </transition>
      </div>
    </div>
  </div>
</template>