<template>
  <div class="flex flex-col bg-[#10375C]/10 mx-2 my-2 rounded-2xl">
    <!-- Header -->
    <span class=" items-center px-4 pt-4 text-3xl text-slate-600">
      <span class="flex flex-row bg-slate-50 text-xl lg:w-2/6 p-3 rounded-2xl shadow-lg">
        <CalendarDays class="h-8 w-8 mr-3"/>{{ DateNow }}
      </span>
    </span>

    <div class="flex flex-row items-center px-4 pt-3 pb-4 text-3xl text-slate-600">
      <span>Report</span>
      <strong class="ml-2">Dashboard</strong>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2">
      <Tabel
        :rows-data="typeRecap"
        :cols="colsDataTypeRecap"
        title1="Recap"
        title2="Type Report"
        :pageable="false"
        :per-page="10"
        :loading="loading"
      />

      <Tabel
        :rows-data="monthRecap"
        :cols="colsDataNominalMonthRecap"
        title1="Recap"
        title2="Nominal Monthly"
        :pageable="true"
        :per-page="10"
        :loading="loading"
      />
    </div>

    <div class="grid grid-cols-1">
      <Chart
        v-if="monthRecapChart.labels.length"
        :chart-data="monthRecapChart"
        title1="Recap"
        title2="Nominal Monthly"
      />
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2">
      <Tabel
        :rows-data="typecustomers"
        :cols="colsDataTypeCustomer"
        title1="Recap Type"
        title2="Customer"
        :pageable="false"
        :per-page="10"
        :loading="loading"
      />

      <Tabel
        v-if="role === 1"
        :rows-data="monthreports"
        :cols="colsDataMonthRecap"
        title1="Recap"
        title2="Offering VS Purchase Order"
        :pageable="true"
        :per-page="10"
        :loading="loading"
      />
    </div>

    <Tabel
      v-if="role === 1"
      :rows-data="customerreports"
      :cols="colsDataCustomerRecap"
      title1="Recap"
      title2="Customer Visit, Follow Up, Offering, Negotiation and Purchase Order"
      :pageable="true"
      :per-page="10"
      :loading="loading"
    />
  </div>
</template>

<script setup>
import Tabel from '@/components/Tabel.vue';
import Chart from '@/components/Chart.vue';
import { ref, onMounted, onUnmounted } from 'vue';
import { CalendarDays } from 'lucide-vue-next';
import axios from 'axios';
import currency from 'currency.js';

/* ===========================
   Helpers
=========================== */
function formatCurrency(value, options = { symbol: 'Rp ', separator: '.', decimal: ',', precision: 0 }) {
  if (value === null || value === undefined || value === '') return '-';
  const num = Number(value);
  if (!Number.isFinite(num)) return '-';
  return currency(num, options).format();
}

/* ===========================
   State
=========================== */
const typecustomers = ref([]);
const monthreports = ref([]);
const customerreports = ref([]);
const monthRecap = ref([]);
const typeRecap = ref([]);
const loading = ref(false);
const DateNow = ref('');

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
   Auth & Config
=========================== */
const token = localStorage.getItem('api_token');
const id = localStorage.getItem('id');
const role = parseInt(localStorage.getItem('role'));
const branch = localStorage.getItem('branch');
const apiBaseUrl = import.meta.env.VITE_API_BASE_URL;

/* ===========================
   Date
=========================== */
const getDate = () => {
  const now = new Date();
  const dayName = now.toLocaleDateString('en-US', { weekday: 'long' });
  const day = String(now.getDate()).padStart(2, '0');
  const month = String(now.getMonth() + 1).padStart(2, '0');
  const year = now.getFullYear();
  DateNow.value = `${dayName}, ${day}/${month}/${year}`;
};

/* ===========================
   Fetch
=========================== */
const fetchTypeReports = async () => {
  let url;
  if (role === 8) url = `${apiBaseUrl}/api/typecustomers/${id}`;
  else if ([7, 6, 5].includes(role)) url = `${apiBaseUrl}/api/typecustomersbybranch/${branch}`;
  else url = `${apiBaseUrl}/api/optiontypecustomers`;

  loading.value = true;
  try {
    const res = await axios.get(url, { headers: { Authorization: `Bearer ${token}` } });
    typecustomers.value = (res.data.data || []).map((i, idx) => ({ ...i, no: idx + 1 }));
  } finally {
    loading.value = false;
  }
};

const fetchMonthReports = async () => {
  let url;
  if (role === 8) url = `${apiBaseUrl}/api/recap-reports/${id}`;
  else if ([7, 6, 5].includes(role)) url = `${apiBaseUrl}/api/recap-reports-branch/${branch}`;
  else url = `${apiBaseUrl}/api/recap-reports`;

  loading.value = true;
  try {
    const res = await axios.get(url, { headers: { Authorization: `Bearer ${token}` } });
    monthreports.value = (res.data.data || []).map((i, idx) => ({ ...i, no: idx + 1 }));
  } finally {
    loading.value = false;
  }
};

const fetchCustomerRecap = async () => {
  loading.value = true;
  try {
    const res = await axios.get(`${apiBaseUrl}/api/recap-reports-customer`, {
      headers: { Authorization: `Bearer ${token}` }
    });
    customerreports.value = (res.data.data || []).map((i, idx) => ({ ...i, no: idx + 1 }));
  } finally {
    loading.value = false;
  }
};

const fetchMonthRecap = async () => {
  loading.value = true;
  try {
    const res = await axios.get(`${apiBaseUrl}/api/recap-nominal-monthly`, {
      headers: { Authorization: `Bearer ${token}` }
    });

    const raw = res.data.data || [];

    monthRecap.value = raw.map((i, idx) => ({
      no: idx + 1,
      month: i.month,
      total: formatCurrency(i.total),
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
  } finally {
    loading.value = false;
  }
};

const fetchTypeRecap = async () => {
  const res = await axios.get(`${apiBaseUrl}/api/recap-reports-type`, {
    headers: { Authorization: `Bearer ${token}` }
  });

  const rows = (res.data.data || []).map((i, idx) => ({
    no: idx + 1,
    report_type: i.report_type,
    total: Number(i.total) || 0,
  }));

  typeRecap.value = [...rows, {
    no: '',
    report_type: 'Grand Total',
    total: Number(res.data.grand_total) || 0,
  }];
};

/* ===========================
   Columns (TIDAK DIUBAH)
=========================== */
const colsDataTypeCustomer = ref([
  { field: 'no', title: 'No', align: 'center' },
  { field: 'type_customer.name', title: 'Type Customer' },
  { field: 'total', title: 'Total', align: 'center' },
]);

const colsDataMonthRecap = ref([
  { field: 'no', title: 'No', align: 'center' },
  { field: 'month', title: 'Month', align: 'center' },
  { field: 'offering', title: 'Offer', align: 'center' },
  { field: 'purchase', title: 'Purchase Order', align: 'center' },
]);

const colsDataCustomerRecap = ref([
  { field: 'no', title: 'No', align: 'center' },
  { field: 'customer_name', title: 'Customer', align: 'left' },
  { field: 'visit', title: 'Visit', align: 'center' },
  { field: 'follow_up', title: 'Follow Up', align: 'center' },
  { field: 'penawaran', title: 'Offering', align: 'center' },
  { field: 'negosiasi', title: 'Negotiation', align: 'center' },
  { field: 'po', title: 'Purchase Order', align: 'center' },
  { field: 'total', title: 'Total', align: 'center' },
]);

const colsDataNominalMonthRecap = ref([
  { field: 'no', title: 'No', align: 'center' },
  { field: 'month', title: 'Month', align: 'center' },
  { field: 'total', title: 'Purchase Total', align: 'right' },
]);

const colsDataTypeRecap = ref([
  { field: 'no', title: 'No', align: 'center' },
  { field: 'report_type', title: 'Report Type', align: 'center' },
  { field: 'total', title: 'Total', align: 'center' },
]);

/* ===========================
   Lifecycle
=========================== */
let timer = null;

onMounted(() => {
  getDate();
  fetchTypeReports();
  fetchMonthReports();
  fetchCustomerRecap();
  fetchMonthRecap();
  fetchTypeRecap();

  timer = setInterval(() => {
    fetchTypeReports();
    fetchMonthReports();
    fetchCustomerRecap();
    fetchMonthRecap();
    fetchTypeRecap();
  }, 5 * 60 * 1000);
});

onUnmounted(() => {
  if (timer) clearInterval(timer);
});
</script>
