<template>
  <div class="px-3 ">
    <div class="px-4 grid grid-cols-1 gap-4  bg-white rounded-3xl shadow-md">
      <!-- Title -->
      <h1 class="text-lg font-bold text-left text-orange-600 px-4 pt-5">
        {{ title1 }}
        <strong class="text-orange-900">{{ title2 }}</strong>
      </h1>

      <!-- Chart -->
      <div class="pb-4">
        <BarChart
          :chart-data="chartData"
          :options="options"
        />
      </div>
    </div>
  </div>
</template>

<script setup>
import { BarChart } from 'vue-chart-3'

const props = defineProps({
  chartData: {
    type: Object,
    required: true,
  },
  title1: String,
  title2: String,
})

const getSuggestedMax = () => {
  const data = props.chartData?.datasets?.[0]?.data || []
  if (!data.length) return undefined
  return Math.max(...data) * 1.2
}

const options = {
  responsive: true,
  maintainAspectRatio: false,

  layout: {
    padding: {
      top: 30,
      bottom: 10,
      left: 10,
      right: 10,
    },
  },

  plugins: {
    legend: { display: false },

    datalabels: {
      anchor: 'end',
      align: 'end',
      font: { weight: 'bold' },
      formatter: (v) =>
        new Intl.NumberFormat('id-ID', {
          notation: 'compact',
          maximumFractionDigits: 1,
        }).format(v),
    },
  },

  scales: {
    y: {
      display: true,
      suggestedMax: getSuggestedMax(),
    },
    x: {
      grid: { display: true },
    },
  },
}
</script>

