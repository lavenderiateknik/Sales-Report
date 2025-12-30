<template>
  <div class="flex flex-col bg-[#10375C]/10 mx-2 my-2 rounded-2xl">

    <!-- Header -->
    <div class="flex flex-row items-center px-4 pt-3 pb-4 text-3xl text-slate-600">
      <span>Report</span>
      <strong class="ml-2 uppercase">{{ role_name }}</strong>
    </div>

    <!-- Filter Sales -->
    <div class="flex flex-row items-center gap-3 px-4 pb-4">
      <label class="text-sm font-medium text-slate-600">Nama Sales</label>
      <select
        v-model="selectedSales"
        @change="fetchRecapData"
        class="border rounded-lg px-3 py-1 text-sm bg-white shadow-sm"
      >
        <option value="">Semua Sales</option>
        <option v-for="sales in salesList" :key="sales.id" :value="sales.id">
          {{ sales.name }}
        </option>
      </select>
    </div>

    <!-- Recap Type Report + Recap Nominal Monthly -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">

      <!-- LEFT: Type Report -->
      <Tabel
        :rows-data="typeRecap"
        :cols="colsDataTypeRecap"
        title1="Recap"
        title2="Type Report"
        :pageable="false"
        :per-page="10"
        :loading="loadingTypeRecap"
      />

      <!-- RIGHT: Nominal Monthly (Dynamic) -->
      <Tabel
        :rows-data="monthlyRecap"
        :cols="colsDataMonthlyRecap"
        title1="Recap"
        title2="Nominal Monthly"
        :pageable="false"
        :per-page="10"
        :loading="loadingMonthly"
      />
    </div>

    <!-- Chart Nominal Monthly -->
    <div class="mt-4" v-if="monthRecapChart.labels.length">
      <Chart
        :chart-data="monthRecapChart"
        title1="Recap"
        title2="Nominal Monthly"
      />
    </div>

    <div class="p-4 text-xs italic text-gray-400">
      Statistik lain belum dihubungkan API — ⭐ next step
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from "vue";
import axios from "axios";
import Tabel from "@/components/Tabel.vue";
import Chart from "@/components/Chart.vue";
import currency from "currency.js";

/* ===========================
   Helpers
=========================== */
function formatCurrency(value, options = { symbol: 'Rp ', separator: '.', decimal: ',', precision: 0 }) {
  if (!value) return '-';
  return currency(Number(value), options).format();
}

/* ===========================
   Auth & Config
=========================== */
const token = localStorage.getItem("api_token");
const role_name = localStorage.getItem("role_name");
const branch = localStorage.getItem("branch");
const apiBaseUrl = import.meta.env.VITE_API_BASE_URL;

/* ===========================
   State
=========================== */
const loadingTypeRecap = ref(false);
const loadingMonthly = ref(false);
const salesList = ref([]);
const selectedSales = ref("");

const typeRecap = ref([]);
const monthlyRecap = ref([]);
const monthRecapChart = ref({
  labels: [],
  datasets: [
    {
      label: 'Nominal Purchase',
      data: [],
      backgroundColor: '#F59E0B',
      borderRadius: 10,
      barThickness: 40,
    }
  ]
});

/* ===========================
   Columns
=========================== */
const colsDataTypeRecap = [
  { field: "report_type", title: "Report Type" },
  { field: "total", title: "Total", align: "center" },
];

const colsDataMonthlyRecap = [
  { field: "no", title: "No", align: "center" },
  { field: "month", title: "Month", align: "center" },
  { field: "nominal", title: "Purchase Total", align: "right" },
];

/* ===========================
   Fetch Sales
=========================== */
const fetchSales = async () => {
  try {
    const res = await axios.get(`${apiBaseUrl}/api/usersbranch/${branch}`, {
      headers: { Authorization: `Bearer ${token}` },
    });
    salesList.value = res.data.data;
  } catch (error) {
    console.error("Fetch Sales Error:", error);
  }
};

/* ===========================
   Fetch Recap Type + Monthly + Chart
=========================== */
const fetchRecapData = async () => {
  // Type Recap
  loadingTypeRecap.value = true;
  let urlType = `${apiBaseUrl}/api/recap-reports-type`;
  if (selectedSales.value) urlType += `?user_id=${selectedSales.value}`;
  try {
    const resType = await axios.get(urlType, {
      headers: { Authorization: `Bearer ${token}` },
    });
    typeRecap.value = resType.data.data;
  } catch (error) {
    console.error("Fetch Recap Type Error:", error);
  } finally {
    loadingTypeRecap.value = false;
  }

  // Monthly Recap
  loadingMonthly.value = true;
  let urlMonthly = `${apiBaseUrl}/api/recap-nominal-monthly`;
  if (selectedSales.value) urlMonthly += `?user_id=${selectedSales.value}`;
  try {
    const resMonthly = await axios.get(urlMonthly, {
      headers: { Authorization: `Bearer ${token}` },
    });

    const raw = resMonthly.data.data || [];
    monthlyRecap.value = raw.map((item, idx) => ({
      no: idx + 1,
      month: item.month,
      nominal: formatCurrency(item.total),
    }));

    monthRecapChart.value = {
      labels: raw.map(i => i.month),
      datasets: [
        {
          label: 'Nominal Purchase',
          data: raw.map(i => Number(i.total) || 0),
          backgroundColor: '#F59E0B',
          borderRadius: 10,
          barThickness: 40,
        }
      ]
    };
  } catch (error) {
    console.error("Fetch Monthly Recap Error:", error);
  } finally {
    loadingMonthly.value = false;
  }
};

/* ===========================
   Mounted
=========================== */
onMounted(async () => {
  await fetchSales();
  await fetchRecapData();
});
</script>
