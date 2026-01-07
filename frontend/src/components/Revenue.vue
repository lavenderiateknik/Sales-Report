<template>
  <div class="p-4 space-y-10 bg-slate-300">
    <!-- Filter Tahun -->
    <div class="bg-white p-4 rounded-2xl shadow flex items-center gap-3 w-fit">
      <label class="text-sm font-semibold text-gray-700">Pilih Tahun:</label>
      <select
        v-model="selectedYear"
        @change="loadData"
        class="border px-3 py-2 rounded-lg text-sm"
      >
        <option v-for="y in years" :key="y" :value="y">
          {{ y }}
        </option>
      </select>
    </div>

    <!-- Chart A: Semua Cabang -->
    <Chart
      v-if="chartAll"
      :chart-data="chartAll"
      title1="Revenue "
      :title2="selectedYear.toString()"
    />

    <!-- Chart B: Per Cabang -->
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
      <Chart
        v-for="item in chartEachBranch"
        :key="item.branch"
        :chart-data="item.data"
        title1="Cabang "
        :title2="item.branch"
      />
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from "vue";
import Chart from "@/components/Chart.vue";

const token = localStorage.getItem("api_token");
const apiBaseUrl = import.meta.env.VITE_API_BASE_URL;

const years = ref([]);
const selectedYear = ref(new Date().getFullYear());

const chartAll = ref(null);
const chartEachBranch = ref([]);

/* =========================
   HELPER FETCH
========================= */
async function apiGet(url, params = {}) {
  const query = new URLSearchParams(params).toString();
  const finalUrl = `${apiBaseUrl}${url}${query ? `?${query}` : ""}`;

  const res = await fetch(finalUrl, {
    headers: {
      Authorization: `Bearer ${token}`,
      Accept: "application/json",
    },
  });

  return await res.json();
}

/* =========================
   LOAD YEARS
========================= */
async function loadYears() {
  const res = await apiGet("/api/available-years");
  years.value = res.years ?? [];
}

/* =========================
   FORMAT DATA
========================= */
const MONTHS = [
  "January","February","March","April","May","June",
  "July","August","September","October","November","December"
];

const formatDataForChart = (rawData) => {
  if (!Array.isArray(rawData)) return [];

  const grouped = rawData.reduce((acc, item) => {
    if (!acc[item.branch]) acc[item.branch] = [];
    acc[item.branch].push({
      month_num: Number(item.month_num),
      total: Number(item.total) || 0,
    });
    return acc;
  }, {});

  return Object.keys(grouped).map(branch => ({
    branch,
    data: MONTHS.map((m, idx) => {
      const found = grouped[branch].find(i => i.month_num === idx + 1);
      return {
        month: m,
        total: found ? found.total : 0
      };
    })
  }));
};

/* =========================
   LOAD DATA
========================= */
const loadData = async () => {
  try {
    const response = await apiGet(
      "/api/recap-nominal-monthly-branches",
      { year: selectedYear.value }
    );

    // 🔥 PENTING: ambil data yang benar
    const raw = response.data ?? response;

    const formatted = formatDataForChart(raw);

    if (!formatted.length) {
      chartEachBranch.value = [];
      chartAll.value = null;
      return;
    }

    /* ======================
       CHART PER CABANG
    ====================== */
    chartEachBranch.value = formatted.map(item => ({
      branch: item.branch,
      data: {
        labels: item.data.map(i => i.month),
        datasets: [
          {
            label: "Revenue",
            data: item.data.map(i => i.total),
          }
        ]
      }
    }));

    /* ======================
       CHART TOTAL SEMUA CABANG
    ====================== */
    const totalByMonth = MONTHS.map((_, idx) =>
      formatted.reduce(
        (sum, b) => sum + (b.data[idx]?.total || 0),
        0
      )
    );

    chartAll.value = {
      labels: MONTHS,
      datasets: [
        {
          label: "Revenue Semua Cabang",
          data: totalByMonth,
        }
      ]
    };

  } catch (error) {
    console.error("Error loading chart", error);
  }
};

onMounted(async () => {
  await loadYears();
  await loadData();
});
</script>
