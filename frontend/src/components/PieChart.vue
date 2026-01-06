<template>
  <div class="bg-white rounded-2xl p-4 shadow w-full">
    <h2 class="text-sm font-semibold text-slate-600 mb-3">
      {{ title }}
    </h2>

    <!-- Jika data kosong -->
    <div
      v-if="!percentageData.length"
      class="text-center text-slate-400 text-sm py-10"
    >
      No data available
    </div>

    <!-- Chart -->
    <canvas v-else ref="canvas"></canvas>
  </div>
</template>

<script setup>
import { ref, onMounted, watch, computed } from "vue";
import {
  Chart,
  PieController,
  ArcElement,
  Tooltip,
  Legend
} from "chart.js";
import ChartDataLabels from "chartjs-plugin-datalabels";

/* =========================
   REGISTER CHART.JS
========================= */
Chart.register(
  PieController,
  ArcElement,
  Tooltip,
  Legend,
  ChartDataLabels
);

/* =========================
   PROPS
========================= */
const props = defineProps({
  data: {
    type: Array,
    required: true
    // format:
    // [{ label: "Assigned", value: 10 }, { label: "Unassigned", value: 5 }]
  },
  title: {
    type: String,
    default: ""
  }
});

/* =========================
   STATE
========================= */
const canvas = ref(null);
let chartInstance = null;

/* =========================
   TOTAL & PERCENTAGE
========================= */
const totalValue = computed(() =>
  props.data.reduce((sum, d) => sum + Number(d.value || 0), 0)
);

const percentageData = computed(() => {
  if (!totalValue.value) return [];

  return props.data.map(d => ({
    label: d.label,
    value: Number(d.value || 0),
    percent: ((d.value / totalValue.value) * 100).toFixed(1)
  }));
});

/* =========================
   RENDER CHART
========================= */
const renderChart = () => {
  if (!canvas.value) return;
  if (!percentageData.value.length) return;

  // Destroy old chart
  if (chartInstance) {
    chartInstance.destroy();
  }

  chartInstance = new Chart(canvas.value, {
    type: "pie",
    data: {
      labels: percentageData.value.map(
        d => `${d.label} (${d.percent}%)`
      ),
      datasets: [
        {
          data: percentageData.value.map(d => d.value),
          backgroundColor: [
            "#0ea5e9", // Assigned
            "#94a3b8"  // Unassigned
          ],
          borderWidth: 1
        }
      ]
    },
    options: {
      responsive: true,
      maintainAspectRatio: true,
      plugins: {
        legend: {
          position: "bottom",
          labels: {
            font: { size: 12 }
          }
        },
        tooltip: {
          callbacks: {
            label: ctx => {
              const d = percentageData.value[ctx.dataIndex];
              return `${d.label}: ${d.value} (${d.percent}%)`;
            }
          }
        },
        // 🔴 Pastikan tidak ada angka mentah di dalam pie
        datalabels: {
          display: false
        }
      }
    }
  });
};

/* =========================
   LIFECYCLE
========================= */
onMounted(renderChart);

// Re-render hanya saat data yang dipakai berubah
watch(percentageData, renderChart);
</script>

<style scoped>
canvas {
  max-height: 320px;
}
</style>
