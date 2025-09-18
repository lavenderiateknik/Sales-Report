<template>
  <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 p-4 w-full h-full">
    <!-- Form -->
      <Transition appear enter-active-class="transition-transform duration-1000 ease-out" enter-from-class="translate-y-40 opacity-0" enter-to-class="translate-x-0 opacity-100">
        <div class="w-full h-full">
          <Checkin v-model:coordinate="coordinate" class="w-full h-full shadow-md rounded-xl" />
        </div>
      </Transition>
    <!-- Map -->
    <div class="w-full">
       <Transition appear enter-active-class="transition-transform duration-1000 ease-out" enter-from-class="translate-x-40 opacity-0" enter-to-class="translate-x-0 opacity-100">
         <Map :coordinate="coordinate" class="w-full h-64 lg:h-full shadow-md rounded-xl" />
       </Transition>
    </div>
  </div>
</template>

<script setup>
import { ref } from "vue";
import Checkin from "@/components/Checkin.vue";
import Map from "@/components/Map.vue";

const coordinate = ref([-6.175869, 106.823055]); // default Jakarta Barat
</script>
