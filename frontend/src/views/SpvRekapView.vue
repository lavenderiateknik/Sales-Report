<template>
  <div>
    <div class="flex flex-col bg-[#10375C]/10 mx-2 my-2 rounded-2xl">
      <!-- Header -->
      <div class="flex flex-row items-center px-4 py-4 text-3xl text-slate-600">
        <span>Supervisor</span>
        <strong class="ml-2">Rekap</strong>
      </div>
      <div class="grid grid-cols-1 lg:grid-cols-2">
        <Tabel :rows-data="typecustomers" :cols="colsDataTypeCustomer" title1="Recap Type" title2="Customer"
          :pageable="false" :per-page="10" :loading="loading" />
        <Tabel :rows-data="monthreports" :cols="colsDataMonthRecap" title1="Recap" title2="Offering VS Purchase Order"
          :pageable="true" :per-page="10" :loading="loading" />
      </div>
      <div class="grid grid-cols-1 lg:grid-cols-2">
        <!-- Nominal monthly -->
        <Tabel :rows-data="monthRecap" :cols="colsDataNominalMonthRecap" title1="Recap" title2="Nominal Monthly"
          :pageable="true" :per-page="10" :loading="loading" />
      </div>
      <!-- recap date -->
      <Tabel :rows-data="dateRecap" :cols="colsDataDateRecap" title1="Recap" title2="By Date" :pageable="true"
        :per-page="10" :loading="loading" />
      <!-- recap by name -->
      <Tabel :rows-data="recapReportByNameCustomers" :cols="colsRecapReportByNameCustomer" title1="Recap"
        title2="Visit, Follow Up, Offering, Negotiation and Purchase Order by Customer" />
    </div>
  </div>
</template>

<script setup>
import Tabel from '@/components/Tabel.vue';
import Chart from '@/components/Chart.vue';
import Bar from '@/components/Bar.vue';
import { ref, onMounted, computed } from 'vue';
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

function formatDate(value) {
  if (!value) return '-';
  const d = new Date(value);
  if (Number.isNaN(d.getTime())) return String(value);
  return d.toLocaleDateString('id-ID', {
    day: '2-digit',
    month: 'long',
    year: 'numeric',
  });
}


const typecustomers = ref([]);
const monthreports = ref([]);
const monthRecap = ref([]);
const dateRecap = ref([]);
const recapReportByNameCustomers = ref([]);

const loading = ref(false);
const url = ref('');

const token = localStorage.getItem('api_token');
const id = localStorage.getItem('id');
const role = parseInt(localStorage.getItem('role'));
const branch = localStorage.getItem('branch');
const apiBaseUrl = import.meta.env.VITE_API_BASE_URL;

const fetchTypeReports = async () => {
  if (role === 8) {
    url.value = `${apiBaseUrl}/api/typecustomers/${id}`;
  } else if ([7, 6, 5].includes(role)) {
    url.value = `${apiBaseUrl}/api/typecustomersbybranch/${branch}`;
  } else {
    url.value = `${apiBaseUrl}/api/optiontypecustomers`;
  }

  loading.value = true;
  try {
    const res = await axios.get(url.value, { headers: { Authorization: `Bearer ${token}` } });
    const data = res.data.data ?? res.data;
    typecustomers.value = (data || []).map((item, idx) => ({ ...item, no: idx + 1 }));
  } catch (err) {
    console.error('Gagal ambil type customers:', err);
  } finally {
    loading.value = false;
  }
};

const colsDataTypeCustomer = ref([
  { field: 'no', title: 'No', align: 'center' },
  { field: 'type_customer.name', title: 'Type Customer', render: (value, row) => value ?? row?.type_customer?.name ?? '-' },
  { field: 'total', title: 'Total', align: 'center', render: (value) => (Number(value) ? value : 0) },
]);

const fetchMonthReports = async () => {
  if (role === 8) {
    url.value = `${apiBaseUrl}/api/recap-reports/${id}`;
  } else if ([7, 6, 5].includes(role)) {
    url.value = `${apiBaseUrl}/api/recap-reports-branch/${branch}`;
  } else {
    url.value = `${apiBaseUrl}/api/recap-reports`;
  }

  loading.value = true;
  try {
    const res = await axios.get(url.value, { headers: { Authorization: `Bearer ${token}` } });
    const data = res.data.data ?? res.data;
    monthreports.value = (data || []).map((item, idx) => ({ ...item, no: idx + 1 }));
  } catch (err) {
    console.error('Gagal ambil month recap :', err);
  } finally {
    loading.value = false;
  }
};

const colsDataMonthRecap = ref([
  { field: 'no', title: 'No', align: 'center' },
  { field: 'month', title: 'Month', align: 'center' },
  { field: 'offering', title: 'Penawaran', align: 'center' },
  { field: 'purchase', title: 'Purchase Order', align: 'center' },
]);

const fetchMonthRecap = async () => {
  loading.value = true;
  try {
    const res = await axios.get(`${apiBaseUrl}/api/recap-nominal-monthly`, {
      headers: { Authorization: `Bearer ${token}` }
    });

    const rows = (res.data.data || []).map((item, idx) => ({
      no: idx + 1,
      month: item.month,
      total: formatCurrency(Number(item.total) || 0), // <-- ubah ke format currency
    }));

    const grand = formatCurrency(Number(res.data.grand_total) || 0); // <-- ubah ke currency
    monthRecap.value = [...rows, { no: '', month: 'Grand Total', total: grand }];
  } catch (err) {
    console.error('Gagal ambil data rekap nominal monthly:', err);
  } finally {
    loading.value = false;
  }
};

const colsDataNominalMonthRecap = ref([
  { field: 'no', title: 'No', align: 'center', filter: false },
  { field: 'month', title: 'Month', align: 'center', filter: false },
  { field: 'total', title: 'Purchase Order', align: 'right', filter: false, render: (value) => (value === null || value === undefined ? '-' : formatCurrency(value)) },
]);

const fetchDateRecap = async () => {
  loading.value = true;
  try {
    const res = await axios.get(`${apiBaseUrl}/api/recap-reports-date`, {
      headers: { Authorization: `Bearer ${token}` }
    });

    dateRecap.value = (res.data.data || []).map((item, idx) => ({
      no: idx + 1,
      date: formatDate(item.date),
      followup: item.follow_up,
      visit: item.visit,
      negotiation: item.negotiation,
      offering: item.offering,
      purchase: item.po,
      total: Number(item.total) || 0, // biarkan angka mentah, biar format pakai render di table
    }));
  } catch (err) {
    console.error('Gagal ambil data rekap per tanggal:', err);
  } finally {
    loading.value = false;
  }
};

const colsDataDateRecap = ref([
  { field: 'date', title: 'Date', align: 'center', filter: false },
  { field: 'visit', title: 'Visit', align: 'center', filter: false },
  { field: 'offering', title: 'Offering', align: 'center', filter: false },
  { field: 'negotiation', title: 'Negotiation', align: 'center', filter: false },
  { field: 'purchase', title: 'Purchase', align: 'center', filter: false },
  { field: 'total', title: 'Total', align: 'right', filter: false, render: (value) => (value === null || value === undefined ? '-' : formatCurrency(value)) },
]);

const fetchRecapByCustomer = async () => {
  loading.value = true;
  try {
    const res = await axios.get(`${apiBaseUrl}/api/recap-report-by-customer`, {
      headers: { Authorization: `Bearer ${token}` }
    });

    const raw = res.data.data || [];
    

    // langsung gunakan data dari API, tambahkan nomor & hitung grand total
    recapReportByNameCustomers.value = raw.map((item, idx) => ({
      no: idx + 1,
      customer: item.customer,
      project_name: item.project_name,
      visit: Number(item.visit) || 0,
      follow_up: Number(item.follow_up) || 0,
      negotiation: Number(item.negotiation) || 0,
      penawaran: Number(item.penawaran) || 0,
      po: Number(item.po) || 0,
      grand_total:
        (Number(item.visit) || 0) +
        (Number(item.follow_up) || 0) +
        (Number(item.negotiation) || 0) +
        (Number(item.penawaran) || 0) +
        (Number(item.po) || 0)
    }));

    // Hitung total keseluruhan
    const totals = recapReportByNameCustomers.value.reduce(
      (acc, cur) => {
        acc.visit += cur.visit;
        acc.follow_up += cur.follow_up;
        acc.negotiation += cur.negotiation;
        acc.penawaran += cur.penawaran;
        acc.po += cur.po;
        acc.grand_total += cur.grand_total;
        return acc;
      },
      { visit: 0, follow_up: 0, negotiation: 0, penawaran: 0, po: 0, grand_total: 0 }
    );

    

  } catch (err) {
    console.error('Gagal ambil recap by customer:', err);
  } finally {
    loading.value = false;
  }
};

const colsRecapReportByNameCustomer = ref([
  { field: 'customer', title: 'Customer', filter: false },
  { field: 'project_name', title: 'Project Name', filter: false },
  { field: 'visit', title: 'Visit', align: 'center' },
  { field: 'follow_up', title: 'Follow Up', align: 'center' },
  { field: 'negotiation', title: 'Negosiasi', align: 'center' },
  { field: 'penawaran', title: 'Penawaran', align: 'center' },
  { field: 'po', title: 'PO', align: 'center' },
  { field: 'grand_total', title: 'Grand Total', align: 'center' }
]);


/* ===========================
   Lifecycle
=========================== */
onMounted(() => {

  fetchTypeReports();
  fetchMonthReports();
  fetchMonthRecap();
  fetchDateRecap();
  fetchRecapByCustomer();

});
</script>