<template>
  <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 p-4 w-full h-full">
    <!-- Form -->
    <div class="w-full h-full">
      <Checkin v-model:coordinate="coordinate" class="w-full h-full shadow-md rounded-xl" />
    </div>

    <!-- Map -->
    <div class="w-full">
      <Map :coordinate="coordinate" class="w-full h-64 lg:h-full shadow-md rounded-xl" />
    </div>
  </div>
</template>

<script setup>
import { ref } from "vue";
import Checkin from "@/components/Checkin.vue";
import Map from "@/components/Map.vue";

const coordinate = ref([-6.175869, 106.823055]); // default Jakarta Barat
</script>
