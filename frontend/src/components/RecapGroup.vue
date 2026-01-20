<template>
  <div class="flex flex-col bg-[#10375C]/10 mx-2 my-2 rounded-2xl">

    <div class="flex flex-row items-center px-4 pt-3 pb-4 text-3xl text-slate-600">
      <span>Report</span>
      <strong class="ml-2 uppercase">{{ role_name }}</strong>
    </div>

    <div class="flex flex-row items-center gap-3 px-4 pb-4">
      <label class="text-sm font-medium text-slate-600">Nama Sales</label>
      <select
        v-model="selectedSales"
        @change="handleFilterChange"
        class="border rounded-lg px-3 py-1 text-sm bg-white shadow-sm"
      >
        <option value="">Semua Sales</option>
        <option v-for="sales in salesList" :key="sales.id" :value="sales.id">
          {{ sales.name }}
        </option>
      </select>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
      <Tabel
        :rows-data="typeRecap"
        :cols="colsDataTypeRecap"
        title1="Recap" title2="Type Report"
        :pageable="false" :per-page="10" :loading="loadingTypeRecap"
      />

      <Tabel
        :rows-data="typeCustomerRecap"
        :cols="colsDataTypeCustomer"
        title1="Recap" title2="Type Customer"
        :pageable="false" :per-page="10" :loading="loadingTypeCustomer"
      />
    </div>

    <div class="grid grid-cols-1 md:grid-cols-1 gap-4 mt-4">
      <Tabel
        :rows-data="monthlyRecap"
        :cols="colsDataMonthlyRecap"
        title1="Recap" title2="Nominal Monthly"
        :pageable="false" :per-page="10" :loading="loadingMonthly"
      />

      <div class="mt-0">
        <Chart
          v-if="!loadingMonthly && monthRecapChart.labels.length"
          :key="chartKey" 
          :chart-data="monthRecapChart"
          title1="Recap"
          title2="Nominal Monthly"
        />
        <div v-else-if="loadingMonthly" class="h-100 flex items-center justify-center bg-white rounded-3xl shadow-md">
           <p class="text-slate-400">Memproses data...</p>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from "vue";
import axios from "axios";
import Tabel from "@/components/Tabel.vue";
import Chart from "@/components/Chart.vue";
import currency from "currency.js";

const token = localStorage.getItem("api_token");
const role_name = localStorage.getItem("role_name");
const role = localStorage.getItem("role");
const branch = localStorage.getItem("branch");
const apiBaseUrl = import.meta.env.VITE_API_BASE_URL;

const loadingTypeRecap = ref(false);
const loadingMonthly = ref(false);
const loadingTypeCustomer = ref(false);
const salesList = ref([]);
const selectedSales = ref("");
const chartKey = ref(0); // Key tambahan untuk force re-render

const typeRecap = ref([]);
const monthlyRecap = ref([]);
const typeCustomerRecap = ref([]);
const monthRecapChart = ref({ labels: [], datasets: [] });

const colsDataTypeRecap = [
  { field: "report_type", title: "Report Type" },
  { field: "total", title: "Total", align: "center" },
];

const colsDataTypeCustomer = [
  { field: "no", title: "No", align: "center" },
  { field: "type_customer.name", title: "Type Customer" },
  { field: "total", title: "Total", align: "center" },
];

const colsDataMonthlyRecap = [
  { field: "no", title: "No", align: "center" },
  { field: "month", title: "Month", align: "center" },
  { field: "nominal", title: "Purchase Total", align: "right" },
];

function formatCurrency(value) {
  if (!value || value == 0) return '-';
  return currency(Number(value), { symbol: 'Rp ', separator: '.', decimal: ',', precision: 0 }).format();
}
const fetchSales = async () => {
  const endpoint = role == 1 | role == 2 | role == 3  ? '/api/allusers' : `/api/usersbranch/${branch}`;
  try {
    const res = await axios.get(`${apiBaseUrl}${endpoint}`, {
    headers: { Authorization: `Bearer ${token}` },
  });
    
    salesList.value = res.data.data;
  } catch (error) { console.error("Sales Error:", error); }
};

const handleFilterChange = async () => {
  // Reset key setiap kali ganti filter untuk "menghancurkan" chart lama segera
  chartKey.value++; 
  await fetchRecapData();
};

const fetchRecapData = async () => {
  loadingTypeRecap.value = true;
  loadingTypeCustomer.value = true;
  loadingMonthly.value = true;

  try {
    // Jalankan semua API
    const [resType, resCust, resMonthly] = await Promise.all([
      axios.get(`${apiBaseUrl}/api/recap-reports-type${selectedSales.value ? '?user_id='+selectedSales.value : ''}`, { headers: { Authorization: `Bearer ${token}` } }),
      axios.get(selectedSales.value ? `${apiBaseUrl}/api/typecustomers/${selectedSales.value}` : `${apiBaseUrl}/api/typecustomersbybranch/${branch}`, { headers: { Authorization: `Bearer ${token}` } }),
      axios.get(`${apiBaseUrl}/api/recap-nominal-monthly-detail${selectedSales.value ? '?user_id='+selectedSales.value : ''}`, { headers: { Authorization: `Bearer ${token}` } })
    ]);

    // 1. Process Type Recap
    typeRecap.value = resType.data.data || [];

    // 2. Process Customer Recap
    typeCustomerRecap.value = (resCust.data.data || []).map((i, idx) => ({
      no: idx + 1,
      type_customer: i.type_customer || { name: i.type_customer_name || "-" },
      total: Number(i.total) || 0,
    }));

    // 3. Process Monthly & Chart
    const rawData = resMonthly.data.data || [];
    const colors = ['#F59E0B', '#10B981', '#3B82F6', '#EF4444', '#8B5CF6', '#06B6D4', '#F472B6'];
    
    let datasets = [];
    if (selectedSales.value) {
      const salesName = salesList.value.find(s => s.id == selectedSales.value)?.name || "";
      datasets = [{
        label: salesName,
        data: rawData.map(m => (m.sales.find(x => x.name === salesName)?.total || 0)),
        backgroundColor: colors[0], borderRadius: 8, barThickness: 50
      }];
    } else {
      const salesSet = new Set();
      rawData.forEach(m => m.sales.forEach(s => { if (s.total > 0) salesSet.add(s.name) }));
      datasets = Array.from(salesSet).map((name, idx) => ({
        label: name,
        data: rawData.map(m => (m.sales.find(x => x.name === name)?.total || 0)),
        backgroundColor: colors[idx % colors.length], borderRadius: 5, barThickness: 30
      }));
    }

    monthRecapChart.value = { labels: rawData.map(m => m.month), datasets };

    monthlyRecap.value = rawData.map((item, idx) => {
      const total = selectedSales.value 
        ? (item.sales.find(x => x.name === salesList.value.find(s => s.id == selectedSales.value)?.name)?.total || 0)
        : item.sales.reduce((sum, s) => sum + Number(s.total), 0);
      return { no: idx + 1, month: item.month, nominal: formatCurrency(total) };
    });

  } catch (e) { console.error("Recap Error:", e); }
  finally {
    loadingTypeRecap.value = false;
    loadingTypeCustomer.value = false;
    loadingMonthly.value = false;
  }
};

onMounted(async () => {
  await fetchSales();
  await fetchRecapData();
});
</script>