<script setup>
import LeftMenuSource from '@/data/structure';
import SideBar from "@/components/sitebar/SideBar.vue";
</script>

<template>
  <div class="container-fluid">
    <div class="row">
      <div class="col-xl-2 col-lg-3 col-md">
        <side-bar :items="LeftMenuSource()" />
      </div>
      <div class="col">
        <router-view />
      </div>
    </div>
  </div>
</template>