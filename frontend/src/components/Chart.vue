<template>
  <div>
    <h1 class="text-lg font-bold text-left text-orange-600">
      {{ title1 }} <strong class="text-orange-900">{{ title2 }}</strong>
    </h1>
    <PieChart :chart-data="chartData" class="h-full bg-amber-600 border-2 border-amber-400 rounded-3xl shadow-md/30 shadow-slate-700" />
  </div>
</template>

<script setup>
import { PieChart } from "vue-chart-3";
import { Chart, registerables } from "chart.js";

Chart.register(...registerables);

defineProps({
  chartData: {
    type: Object,
    required: true,
  },
  title1: { type: String, default: "My Table" },
  title2: { type: String, default: "My Table" },
});
</script>
