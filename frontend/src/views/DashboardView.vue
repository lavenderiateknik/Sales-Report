<template>
  <div class="flex flex-col bg-[#10375C]/10 mx-2 my-2 rounded-2xl">
    <!-- Header -->
    <div class="flex flex-row items-center px-4 py-4 text-3xl text-slate-600">
      <span>Report</span>
      <strong class="ml-2">Dashboard</strong>
    </div>

    <!-- Tabel utama -->
    <Tabel
      :rows-data="customers"
      :cols="colsData"
      title1="Visit"
      title2="Daily"
      :pageable="true"
      :per-page="10"
      :loading="loading"
    />
    <div class="grid grid-cols-1 lg:grid-cols-2">
      <!-- Tabel Type Customer -->
      <Tabel
        :rows-data="typecustomers"
        :cols="colsDataTypeCustomer"
        title1="Recap Type"
        title2="Customer"
        :pageable="false"
        :per-page="10"
        :loading="loading"
      />
      <!-- Month recap offering vs po -->
      <Tabel
        :rows-data="monthreports"
        :cols="colsDataMonthRecap"
        title1="Recap"
        title2="Offering VS Purchase Order"
        :pageable="true"
        :per-page="10"
        :loading="loading"
      />
    </div>
    
    <!-- Recap per customer -->
    <Tabel
      :rows-data="customerreports"
      :cols="colsDataCustomerRecap"
      title1="Recap"
      title2="Customer Visit, Follow Up, Offering, Negotiation and Purchase Order"
      :pageable="true"
      :per-page="10"
      :loading="loading"
    />

    <div class="grid grid-cols-1 lg:grid-cols-2">
      <!-- Nominal monthly -->
      <Tabel
        :rows-data="monthRecap"
        :cols="colsDataNominalMonthRecap"
        title1="Recap"
        title2="Nominal Monthly"
        :pageable="true"
        :per-page="10"
        :loading="loading"
      />
      <!-- Type report recap -->
      <Tabel
        :rows-data="typeRecap"
        :cols="colsDataTypeRecap"
        title1="Recap"
        title2="Type Report"
        :pageable="false"
        :per-page="10"
        :loading="loading"
      />
    </div>
  </div>
</template>

<script setup>
import Tabel from '@/components/Tabel.vue';
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
/* ===========================
   State
=========================== */
const customers = ref([]);
const typecustomers = ref([]);
const monthreports = ref([]);
const customerreports = ref([]);
const monthRecap = ref([]);
const typeRecap = ref([]);
const loading = ref(false);
const url = ref('');

const token = localStorage.getItem('api_token');
const id = localStorage.getItem('id');
const role = parseInt(localStorage.getItem('role'));
const branch = localStorage.getItem('branch');
const apiBaseUrl = import.meta.env.VITE_API_BASE_URL;

/* ===========================
   Fetch Functions
=========================== */
const fetchSalesReports = async () => {
  if (role === 8) {
    url.value = `${apiBaseUrl}/api/salesreports/${id}`;
  } else if ([7, 6, 5].includes(role)) {
    url.value = `${apiBaseUrl}/api/branchsalesreports/${branch}`;
  } else {
    url.value = `${apiBaseUrl}/api/allsalesreports`;
  }

  loading.value = true;
  try {
      const res = await axios.get(url.value, { headers: { Authorization: `Bearer ${token}` } });
      const data = res.data.data ?? res.data;
      customers.value = data.map((item, idx) => ({
    ...item,
        no: idx + 1,
    date: formatDate(item.date),
    nominal_purchase_order: formatCurrency(item.nominal_purchase_order)
  }));
  } catch (err) {
    console.error('Gagal ambil sales reports:', err);
  } finally {
    loading.value = false;
  }
};

const fetchTypeReports = async () => {
  if (role === 8) {
    url.value = `${apiBaseUrl}/api/typecustomers/${id}`;
  } else if ([7, 6, 5].includes(role)) {
    url.value = `${apiBaseUrl}/api/typecustomersbybranch/${branch}`;
  } else {
    url.value = `${apiBaseUrl}/api/alltypecustomers`;
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

const fetchCustomerRecap = async () => {
  url.value = `${apiBaseUrl}/api/recap-reports-customer`;
  loading.value = true;
  try {
    const res = await axios.get(url.value, { headers: { Authorization: `Bearer ${token}` } });
    const data = res.data.data ?? res.data;
    customerreports.value = (data || []).map((item, idx) => ({ ...item, no: idx + 1 }));
  } catch (err) {
    console.error('Gagal ambil customer recap:', err);
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


const fetchTypeRecap = async () => {
  try {
    const res = await axios.get(`${apiBaseUrl}/api/recap-reports-type`, { headers: { Authorization: `Bearer ${token}` } });
    const rows = (res.data.data || []).map((item, idx) => ({
      no: idx + 1,
      report_type: item.report_type,
      total: Number(item.total) || 0,
    }));
    const grand = Number(res.data.grand_total) || 0;
    typeRecap.value = [...rows, { no: '', report_type: 'Grand Total', total: grand }];
  } catch (err) {
    console.error('Gagal ambil recap type:', err);
  }
};

/* ===========================
   Kolom Tabel
=========================== */
const colsData = ref([
  { field: 'no', title: 'No', align: 'center', minWidth: '60px', filter: false },
  {
    field: 'date',
    title: 'Tanggal',
    type: 'date',
    minWidth: '150px',
    align: 'center',
    render: (value) => (value ? formatDate(value) : '-'),
  },
  { field: 'customer_name', title: 'Name Customer', render: (value) => value ?? '-' },
  { field: 'user.name', title: 'Sales Name', render: (value, row) => value ?? row?.user?.name ?? '-' },
  { field: 'type_customer.name', title: 'Type Customer', render: (value, row) => value ?? row?.type_customer?.name ?? '-' },
  { field: 'type_project.name', title: 'Type Project', render: (value, row) => value ?? row?.type_project?.name ?? '-' },
  { field: 'project_name', title: 'Project Name', render: (value) => value ?? '-' },
  { field: 'pic_name', title: 'PIC Name', filter: false, render: (value) => value ?? '-' },
  { field: 'pic_phone', title: 'PIC Phone', filter: false, render: (value) => value ?? '-' },
  { field: 'pic_position', title: 'PIC Position', filter: false, render: (value) => value ?? '-' },
  { field: 'type_report.name', title: 'Type Report', filter: false, render: (value, row) => value ?? row?.type_report?.name ?? '-' },
  { field: 'report_notes', title: 'Report Notes', filter: false, cellClass: 'wrap-cell', minWidth: '200px', render: (value) => value ?? '-' },
  { field: 'equipment_needs', title: 'Equipments Needs', render: (value) => value ?? '-' },
  { field: 'items_purchase_order', title: 'Items Purchase Order', filter: false, render: (value) => value ?? '-' },
  {
  field: 'nominal_purchase_order',
  title: 'Estimated Nominal Purchase',
  align: 'right',
  filter: false,
  cell: (row) => {
    return formatCurrency(row.nominal_purchase_order);
  }
}

]);

const colsDataTypeCustomer = ref([
  { field: 'no', title: 'No', align: 'center' },
  { field: 'type_customer.name', title: 'Type Customer', render: (value, row) => value ?? row?.type_customer?.name ?? '-' },
  { field: 'total', title: 'Total', align: 'center', render: (value) => (Number(value) ? value : 0) },
]);

const colsDataMonthRecap = ref([
  { field: 'no', title: 'No', align: 'center' },
  { field: 'month', title: 'Bulan', align: 'center' },
  { field: 'offering', title: 'Penawaran', align: 'center' },
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
  { field: 'no', title: 'No', align: 'center', filter: false },
  { field: 'month', title: 'Bulan', align: 'center', filter: false },
  { field: 'total', title: 'Jumlah Nominal', align: 'right', filter: false, render: (value) => (value === null || value === undefined ? '-' : formatCurrency(value)) },
]);

const colsDataTypeRecap = ref([
  { field: 'no', title: 'No', align: 'center', filter: false },
  { field: 'report_type', title: 'Report Type', align: 'center', filter: false },
  { field: 'total', title: 'Total', align: 'center', filter: false, render: (value) => (value === null || value === undefined ? '-' : formatCurrency(value)) },
]);

/* ===========================
   Lifecycle
=========================== */
onMounted(() => {
  fetchSalesReports();
  fetchTypeReports();
  fetchMonthReports();
  fetchCustomerRecap();
  fetchMonthRecap();
  fetchTypeRecap();
});
</script>

<style scoped>
::deep .bh-table {
  table-layout: fixed;
  width: 100%;
}
::deep .bh-items-center {
  white-space: normal !important;
  word-break: break-word;
  overflow-wrap: break-word;
}
::deep .bh-table td {
  overflow: hidden;
  text-overflow: ellipsis;
  white-space: nowrap;
}
::deep .bh-table td.wrap-cell {
  white-space: normal !important;
  word-break: break-word;
  overflow-wrap: break-word;
}
</style>
