<script setup>
import { RouterView, useRoute } from "vue-router";
import Navigation from "./components/Navigation.vue";

const route = useRoute();
</script>

<template>
  <!-- cek route -->
  <div
    class="h-auto lg:h-dvh"
    :class="route.name === 'Login' ? 'grid grid-cols-1' : 'grid grid-cols-1 lg:grid-cols-6'"
  >
    <!-- Navigation tampil hanya kalau bukan login -->
    <Navigation
      v-if="route.name !== 'Login'"
      class="bg-[#10375C] w-full h-auto lg:h-full shadow-md shadow-slate-700 rounded-tr-3xl"
    />

    <!-- Main Content -->
    <div 
      class="w-full h-full overflow-y-auto"
      :class="route.name === 'Login' ? 'col-span-1' : 'col-span-1 lg:col-span-5'"
    >
      <RouterView />
    </div>
  </div>
</template>
