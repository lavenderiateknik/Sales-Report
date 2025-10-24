<template>
  <div class="bg-slate-100 rounded-xl border border-amber-500 shadow-xl/40 shadow-slate-700">
    <l-map
      style="height: 100%; width: 100%;"
      :zoom="15"
      :center="coordinate"
      class="rounded-xl"
    >
      <l-tile-layer
        url="https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png"
        attribution="&copy; OpenStreetMap contributors"
      />
      <l-marker :lat-lng="coordinate" />
    </l-map>
  </div>
</template>

<script setup>
import { LMap, LTileLayer, LMarker } from "@vue-leaflet/vue-leaflet";
import "leaflet/dist/leaflet.css";

defineProps({
  coordinate: {
    type: Array,
    default: () => [-6.175869, 106.823055],
  },
});
</script>
