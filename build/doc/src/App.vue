<script setup>
import LeftMenuSource from '@/data/structure';
import SideBar from "@/components/sitebar/SideBar.vue";
</script>

<template>
  <div class="container-fluid">
    <div class="row">
      <div class="col-lg-3 col-md-6">
        <side-bar :items="LeftMenuSource()" />
      </div>
      <div class="col-lg-7 col-md-6">
        <router-view />
      </div>
    </div>
  </div>
</template>