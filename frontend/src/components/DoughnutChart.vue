<template>
  <div class="bg-white rounded-2xl p-4 shadow w-full flex flex-col items-center">
    <h2 class="text-sm font-semibold text-slate-600 mb-3">
      {{ title }}
    </h2>

    <!-- Empty state -->
    <div
      v-if="!percentageData.length"
      class="text-center text-slate-400 text-sm py-12"
    >
      No data available
    </div>

    <!-- Chart -->
    <div v-else class="relative w-72 h-72">
      <canvas ref="canvas"></canvas>

      <!-- Center Text -->
      <div
        class="absolute inset-0 flex flex-col items-center justify-center text-center pointer-events-none"
      >
        <div class="text-2xl font-bold text-slate-700">
          {{ assignedPercent }}%
        </div>
        <div class="text-xs text-slate-500">
          Assigned
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, watch, computed } from "vue";
import {
  Chart,
  DoughnutController,
  ArcElement,
  Tooltip,
  Legend
} from "chart.js";
import ChartDataLabels from "chartjs-plugin-datalabels";

/* =========================
   REGISTER CHART.JS
========================= */
Chart.register(
  DoughnutController,
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
    // [{ label: "Assigned", value: 1 }, { label: "Unassigned", value: 708 }]
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
   CENTER TEXT (ASSIGNED)
========================= */
const assignedPercent = computed(() => {
  const assigned = percentageData.value.find(
    d => d.label.toLowerCase() === "assigned"
  );
  return assigned ? assigned.percent : "0.0";
});

/* =========================
   RENDER CHART
========================= */
const renderChart = () => {
  if (!canvas.value) return;
  if (!percentageData.value.length) return;

  if (chartInstance) {
    chartInstance.destroy();
  }

  chartInstance = new Chart(canvas.value, {
    type: "doughnut",
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
          borderWidth: 2,
          borderColor: "#ffffff",
          offset: ctx =>
            ctx.raw > 0 && ctx.raw / totalValue.value < 0.02 ? 8 : 0
        }
      ]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      cutout: "65%",
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
watch(percentageData, renderChart);
</script>

<style scoped>
canvas {
  width: 100% !important;
  height: 100% !important;
}
</style>
