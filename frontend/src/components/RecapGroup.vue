<template>
  <div class="flex flex-col bg-[#10375C]/10 mx-2 my-2 rounded-2xl">
    <div v-if="isAllowed">

      <!-- HEADER -->
      <div class="flex flex-row items-center px-4 pt-3 pb-4 text-3xl text-slate-600">
        <span>Report</span>
        <strong class="ml-2 uppercase">{{ role_name }}</strong>
      </div>

      <!-- CABANG -->
      <div v-if="[1, 2, 3].includes(role)" class="flex flex-row items-center gap-3 px-4 pb-2">
        <label class="text-sm font-medium text-slate-600">Cabang</label>
        <select v-model="selectedBranch" @change="handleFilterChange"
          class="border rounded-lg px-3 py-1 text-sm bg-white shadow-sm">
          <option value="">Semua Cabang</option>
          <option v-for="b in branchList" :key="b.id" :value="b.id">
            {{ b.name }}
          </option>
        </select>
      </div>

      <!-- SALES -->
      <div class="flex flex-row items-center gap-3 px-4 pb-2">
        <label class="text-sm font-medium text-slate-600">Nama Sales</label>
        <select v-model="selectedSales" @change="handleFilterChange"
          class="border rounded-lg px-3 py-1 text-sm bg-white shadow-sm">
          <option value="">Semua Sales</option>
          <option v-for="s in salesList" :key="s.id" :value="s.id">
            {{ s.name }}
          </option>
        </select>
      </div>

      <!-- PERIODE -->
      <div class="flex flex-row items-center gap-3 px-4 pb-4">
        <label class="text-sm font-medium text-slate-600">Periode</label>
        <input type="date" v-model="startDate" @change="handleFilterChange"
          class="border rounded-lg px-3 py-1 text-sm bg-white shadow-sm" />
        <span class="text-slate-500">s/d</span>
        <input type="date" v-model="endDate" @change="handleFilterChange"
          class="border rounded-lg px-3 py-1 text-sm bg-white shadow-sm" />
      </div>

      <!-- TABLES -->
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
        <Tabel :rows-data="typeRecap" :cols="colsDataTypeRecap" title1="Recap" title2="Type Report" :pageable="false"
          :per-page="10" :loading="loadingTypeRecap" />

        <Tabel :rows-data="typeCustomerRecap" :cols="colsDataTypeCustomer" title1="Recap" title2="Type Customer"
          :pageable="false" :per-page="10" :loading="loadingTypeCustomer" />
      </div>

      <div class="grid grid-cols-1 gap-4 mt-4">
        <Tabel :rows-data="monthlyRecap" :cols="colsDataMonthlyRecap" title1="Recap" title2="Nominal Monthly"
          :pageable="false" :per-page="10" :loading="loadingMonthly" />

        <Chart v-if="!loadingMonthly && monthRecapChart.labels.length" :key="chartKey" :chart-data="monthRecapChart"
          title1="Recap" title2="Nominal Monthly" />
      </div>

    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from "vue";
import axios from "axios";
import Tabel from "@/components/Tabel.vue";
import Chart from "@/components/Chart.vue";
import currency from "currency.js";

/* ================= AUTH ================= */
const token = localStorage.getItem("api_token");
const role_name = localStorage.getItem("role_name");
const role = Number(localStorage.getItem("role"));
const branch = localStorage.getItem("branch");
const apiBaseUrl = import.meta.env.VITE_API_BASE_URL;

const isAllowed = computed(() => role <= 6);
const authHeader = { headers: { Authorization: `Bearer ${token}` } };

/* ================= STATE ================= */
const salesList = ref([]);
const branchList = ref([]);

const selectedSales = ref("");
const selectedBranch = ref("");

const startDate = ref("");
const endDate = ref("");

const typeRecap = ref([]);
const typeCustomerRecap = ref([]);
const monthlyRecap = ref([]);
const monthRecapChart = ref({ labels: [], datasets: [] });

const loadingTypeRecap = ref(false);
const loadingTypeCustomer = ref(false);
const loadingMonthly = ref(false);

const chartKey = ref(0);

/* ================= COLS ================= */
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
  { field: "month", title: "Month" },
  { field: "nominal", title: "Purchase Total", align: "right" },
];

/* ================= HELPER ================= */
const buildDateParams = () => {
  let p = "";
  if (startDate.value) p += `&start_date=${startDate.value}`;
  if (endDate.value) p += `&end_date=${endDate.value}`;
  return p;
};

const formatCurrency = (v) =>
  currency(Number(v || 0), { symbol: "Rp ", precision: 0 }).format();

/* ================= FETCH ================= */

const fetchSales = async () => {
  const endpoint = [1, 2, 3].includes(role)
    ? "/api/allusers"
    : `/api/usersbranch/${branch}`;

  const res = await axios.get(apiBaseUrl + endpoint, authHeader);
  salesList.value = res.data.data;
};

const fetchBranches = async () => {
  if (![1, 2, 3].includes(role)) return;
  const res = await axios.get(apiBaseUrl + "/api/allbranches", authHeader);
  branchList.value = res.data.data;
};

const handleFilterChange = async () => {
  chartKey.value++;
  await fetchRecapData();
};

const fetchRecapData = async () => {
  loadingTypeRecap.value = true;
  loadingTypeCustomer.value = true;
  loadingMonthly.value = true;

  const isGlobal = [1, 2, 3].includes(role);

  try {

    /* ===== TYPE ===== */
    let typeUrl = `${apiBaseUrl}/api/recap-reports-type?1=1`;

    if (selectedSales.value) typeUrl += `&user_id=${selectedSales.value}`;
    else if (isGlobal && selectedBranch.value) typeUrl += `&branch_id=${selectedBranch.value}`;
    else if (!isGlobal) typeUrl += `&branch_id=${branch}`;

    typeUrl += buildDateParams();

    /* ===== CUSTOMER ===== */
    let custUrl = selectedSales.value
      ? `${apiBaseUrl}/api/typecustomers/${selectedSales.value}?1=1`
      : isGlobal && selectedBranch.value
        ? `${apiBaseUrl}/api/typecustomersbybranch/${selectedBranch.value}?1=1`
        : isGlobal
          ? `${apiBaseUrl}/api/optiontypecustomers?1=1`
          : `${apiBaseUrl}/api/typecustomersbybranch/${branch}?1=1`;

    custUrl += buildDateParams();

    /* ===== MONTHLY ===== */
    let monthUrl = `${apiBaseUrl}/api/recap-nominal-monthly-detail?1=1`;

    if (selectedSales.value) monthUrl += `&user_id=${selectedSales.value}`;
    else if (isGlobal && selectedBranch.value) monthUrl += `&branch_id=${selectedBranch.value}`;
    else if (!isGlobal) monthUrl += `&branch_id=${branch}`;

    monthUrl += buildDateParams();

    const [r1, r2, r3] = await Promise.all([
      axios.get(typeUrl, authHeader),
      axios.get(custUrl, authHeader),
      axios.get(monthUrl, authHeader)
    ]);

    typeRecap.value = r1.data.data || [];

    typeCustomerRecap.value = (r2.data.data || []).map((i, idx) => ({
      no: idx + 1,
      type_customer: i.type_customer || { name: i.type_customer_name },
      total: Number(i.total)
    }));

    const raw = r3.data.data || [];

    monthlyRecap.value = raw.map((item, idx) => {
      const total = selectedSales.value
        ? item.sales.find(s =>
          s.name === salesList.value.find(x => x.id == selectedSales.value)?.name
        )?.total || 0
        : item.sales.reduce((sum, s) => sum + Number(s.total), 0);

      return {
        no: idx + 1,
        month: item.month,
        nominal: formatCurrency(total)
      };
    });

    const colors = ['#F59E0B', '#10B981', '#3B82F6', '#EF4444', '#8B5CF6', '#06B6D4'];

    let datasets = [];

    if (selectedSales.value) {

      const salesName =
        salesList.value.find(s => s.id == selectedSales.value)?.name || "";

      datasets = [{
        label: salesName,
        data: raw.map(m =>
          m.sales.find(x => x.name === salesName)?.total || 0
        ),
        backgroundColor: colors[0],
        borderRadius: 8,
        barThickness: 50,
      }];

    } else {

      const salesSet = new Set();

      raw.forEach(m =>
        m.sales.forEach(s => {
          if (Number(s.total) > 0) salesSet.add(s.name);
        })
      );

      datasets = Array.from(salesSet).map((name, idx) => ({
        label: name,
        data: raw.map(m =>
          m.sales.find(x => x.name === name)?.total || 0
        ),
        backgroundColor: colors[idx % colors.length],
        borderRadius: 5,
        barThickness: 30,
      }));
    }

    monthRecapChart.value = {
      labels: raw.map(m => m.month),
      datasets
    };


  } catch (e) {
    console.error(e);
  } finally {
    loadingTypeRecap.value = false;
    loadingTypeCustomer.value = false;
    loadingMonthly.value = false;
  }
};

/* ================= INIT ================= */
onMounted(async () => {
  await fetchSales();
  await fetchBranches();
  await fetchRecapData();
});
</script>