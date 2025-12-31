<template>
  <div class="px-3 py-3 ">
    <div class="px-4 grid grid-cols-1 gap-4 bg-white rounded-3xl shadow-md">
      <h1 class="text-lg font-bold text-left text-orange-600 px-4 pt-5">
        {{ title1 }}
        <strong class="text-orange-900">{{ title2 }}</strong>
      </h1>

      <div class="pb-4 h-100"> <BarChart
          :chart-data="chartData"
          :options="options"
        />
      </div>
    </div>
  </div>
</template>

<script setup>
import { BarChart } from 'vue-chart-3'
import { Chart, registerables } from 'chart.js'
import ChartDataLabels from 'chartjs-plugin-datalabels'

// Pastikan plugin terdaftar
Chart.register(...registerables, ChartDataLabels)

const props = defineProps({
  chartData: {
    type: Object,
    required: true,
  },
  title1: String,
  title2: String,
})

const options = {
  responsive: true,
  maintainAspectRatio: false,
  layout: {
    padding: { top: 20, bottom: 10, left: 10, right: 10 }
  },
  plugins: {
    // 1. Tooltip Informatif (Muncul saat Hover)
    tooltip: {
      callbacks: {
        label: function(context) {
          let label = context.dataset.label || '';
          if (label) {
            label += ': ';
          }
          if (context.parsed.y !== null) {
            // Menampilkan format rupiah lengkap di tooltip
            label += new Intl.NumberFormat('id-ID', {
              style: 'currency',
              currency: 'IDR',
              maximumFractionDigits: 0
            }).format(context.parsed.y);
          }
          return label;
        }
      }
    },
    // 2. Legend yang lebih rapi
    legend: {
      display: true,
      position: 'top',
      labels: {
        usePointStyle: true, // Bentuk lingkaran kecil agar manis
        padding: 20,
        font: { size: 12 }
      }
    },
    // 3. Optimasi Datalabels (Label di dalam bar)
    datalabels: {
      color: '#475569',
      anchor: 'center',
      align: 'center',
      display: 'auto', // OTOMATIS SEMBUNYI jika bar terlalu sempit/kecil
      font: {
        weight: 'bold',
        size: 11
      },
      formatter: (value) => {
        if (value === 0) return null; // Sembunyikan jika nol
        return new Intl.NumberFormat('id-ID', {
          notation: 'compact',
          maximumFractionDigits: 1,
        }).format(value);
      },
    },
  },
  scales: {
    x: { 
      stacked: true, 
      grid: { display: false } 
    },
    y: { 
      stacked: true, 
      beginAtZero: true,
      ticks: {
        // Format sumbu Y agar lebih bersih (misal: 100 jt)
        callback: (value) => {
          return new Intl.NumberFormat('id-ID', {
            notation: 'compact',
          }).format(value);
        }
      }
    },
  }
};
</script>