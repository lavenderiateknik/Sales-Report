<template>
  <div class="flex flex-col bg-[#10375C]/10 mx-2 my-2 rounded-2xl">
    <!-- Header -->
    <div class="flex flex-row items-center px-4 py-4 text-3xl text-slate-600">
      <span>Report</span>
      <strong class="ml-2">Dashboard</strong>
    </div>

    <!-- Tabel -->
    <Tabel :rows-data="customers" :cols="colsData" title1="Visit" title2="Daily" :pageable="true" :per-page="10"
      :loading="loading" />

    <div class="grid grid-cols-2">
      <!-- Tabel Type Customer -->
      <Tabel :rows-data="typecustomers" :cols="colsDataTypeCustomer" title1="Recap Type" title2="Customer"
        :pageable="true" :per-page="10" :loading="loading" />
      <Tabel :rows-data="monthreports" :cols="colsDataMonthRecap" title1="Recap" title2="Offering VS Purchase Order"
        :pageable="true" :per-page="10" :loading="loading" />
    </div>

  </div>
</template>

<script setup>
import Tabel from '@/components/Tabel.vue';
import { ref, onMounted } from 'vue';
import axios from 'axios';

const customers = ref([]);
const typecustomers = ref([]);
const monthreports = ref([]);
const loading = ref(false);
const url = ref('');

const token = localStorage.getItem('api_token');
const id = localStorage.getItem('id');
const role = parseInt(localStorage.getItem('role'));
const branch = localStorage.getItem('branch');
const apiBaseUrl = import.meta.env.VITE_API_BASE_URL;

// Ambil data sales reports
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
    const res = await axios.get(url.value, {
      headers: { Authorization: `Bearer ${token}` },
    });

    const data = res.data.data ?? res.data;
    customers.value = data.map((item, idx) => ({
      ...item,
      no: idx + 1
    }));
  } catch (err) {
    console.error('Gagal ambil sales reports:', err);
  } finally {
    loading.value = false;
  }
};

// Kolom tabel sales reports
const colsData = ref([
  { field: 'no', title: 'No', align: 'center', minWidth: "60px" },
  {
    field: 'date',
    title: 'Tanggal',
    type: 'date',
    minWidth: "150px",
    filter: false,
    align: 'center',
    render: (row) => row.date ? new Date(row.date).toLocaleDateString('id-ID') : '-',
  },
  { field: 'type_customer.name', title: 'Type Customer', render: (row) => row.type_customer?.name ?? '-' },
  { field: 'type_project.name', title: 'Type Project', render: (row) => row.type_project?.name ?? '-' },
  { field: 'project_name', title: 'Penawaran' },
  { field: 'pic_name', title: 'PIC Name' },
  { field: 'pic_phone', title: 'PIC Phone' },
  { field: 'pic_position', title: 'PIC Position' },
  { field: 'type_report.name', title: 'Type Report', render: (row) => row.type_report?.name ?? '-' },
  { field: 'report_notes', title: 'Report Notes', cellClass: "wrap-cell", minWidth: "200px" },
  { field: 'equipment_needs', title: 'Kebutuhan Alat' },
  { field: 'items_purchase_order', title: 'Items Purchase Order' },
  {
    field: 'nominal_purchase_order',
    title: 'Estimated Nominal Purchase',
    align: 'right',
    render: (row) => row.nominal_purchase_order
      ? new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(row.nominal_purchase_order)
      : '-',
  },
]);

// Ambil data type customers
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
    const res = await axios.get(url.value, {
      headers: { Authorization: `Bearer ${token}` },
    });

    const data = res.data.data ?? res.data;
    typecustomers.value = data.map((item, idx) => ({
      ...item,
      no: idx + 1
    }));
  } catch (err) {
    console.error('Gagal ambil type customers:', err);
  } finally {
    loading.value = false;
  }
};

// Kolom tabel type customers
const colsDataTypeCustomer = ref([
  { field: 'no', title: 'No', align: 'center' },
  { field: 'type_customer.name', title: 'Type Customer', render: (row) => row.type_customer?.name ?? '-' },
  { field: 'total', title: 'Total', align: 'center', render: (row) => row.total ?? 0 },
]);

// Ambil data recap bulanan
const monthrecap = async () => {
  if (role === 8) {
    url.value = `${apiBaseUrl}/api/recap-reports/${id}`;
  } else if ([7, 6, 5].includes(role)) {
    url.value = `${apiBaseUrl}/api/recap-reports-branch/${branch}`; // <- belum dibuat
  } else {
    url.value = `${apiBaseUrl}/api/recap-reports`;
  }

  loading.value = true;
  try {
    const res = await axios.get(url.value, {
      headers: { Authorization: `Bearer ${token}` },
    });

    const data = res.data.data ?? res.data;
    monthreports.value = data.map((item, idx) => ({
      ...item,
      no: idx + 1
    }));
  } catch (err) {
    console.error('Gagal ambil month recap :', err);
  } finally {
    loading.value = false;
  }
};

// Kolom tabel month recap
const colsDataMonthRecap = ref([
  { field: 'no', title: 'No', align: 'center' },
  { field: 'month', title: 'Bulan', align: 'center' },
  { field: 'offering', title: 'Penawaran', align: 'center' },
  { field: 'purchase', title: 'Purchase Order', align: 'center' },
]);

onMounted(() => {
  fetchSalesReports();
  fetchTypeReports();
  monthrecap();
});
</script>


<style scoped>
/* paksa plugin supaya pake fixed layout */
::deep .bh-table {
  table-layout: fixed;
  width: 100%;
}

/* Semua header bisa wrap */
::deep .bh-items-center {
  white-space: normal !important;
  word-break: break-word;
  overflow-wrap: break-word;
}

/* Default isi cell tetap ellipsis */
::deep .bh-table td {
  overflow: hidden;
  text-overflow: ellipsis;
  white-space: nowrap;
}

/* Khusus cell panjang (misal report_notes) bisa wrap */
::deep .bh-table td.wrap-cell {
  white-space: normal !important;
  word-break: break-word;
  overflow-wrap: break-word;
}
</style>
