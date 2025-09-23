<template>
  <div class="flex flex-col bg-[#10375C]/10 mx-2 my-2 rounded-2xl">
    <!-- Header -->
    <div class="flex flex-row items-center px-4 py-4 text-3xl text-slate-600">
      <span>Report</span>
      <strong class="ml-2">Dashboard</strong>
    </div>

    <!-- Tabel -->
    <Tabel
      :rows-data="customers"
      :cols="colsData"
      title1="Visit"
      title2="Daily"
      :pageable="true"
      :per-page="10"
      :loading="loading"
    />

    
  </div>
</template>

<script setup>
import Tabel from '@/components/Tabel.vue';
import { ref, onMounted } from 'vue';
import axios from 'axios';

// Data tabel
const customers = ref([]);
const loading = ref(false);
const url = ref('');

// Token dan API
const token = localStorage.getItem('api_token');
const id = localStorage.getItem('id');
const role = parseInt(localStorage.getItem('role'));
const branch = localStorage.getItem('branch');

if (role === 8) {
  url.value = `http://127.0.0.1:8000/api/salesreports/${id}`;
} else if ([7, 6, 5].includes(role)) {
  url.value = `http://127.0.0.1:8000/api/branchsalesreports/${branch}`;
} else {
  url.value = 'http://127.0.0.1:8000/api/allsalesreports';
}
console.log('API URL:', url.value);

// Ambil data dari API
const fetchSalesReports = async () => {
  loading.value = true;
  try {
    const res = await axios.get(url.value, {
      headers: { Authorization: `Bearer ${token}` },
    });

    // Pastikan sesuai struktur API Anda
    customers.value = res.data.data ?? res.data;

    console.log('Customers:', customers.value);
  } catch (err) {
    console.error('Gagal ambil sales reports:', err);
  } finally {
    loading.value = false;
  }
};

// Kolom tabel
const colsData = ref([
  {
    field: 'date',
    title: 'Tanggal',
    type: 'date',
    minWidth: "150px",
    filter: false,
    align: 'center',
    render: (row) => row.date ? new Date(row.date).toLocaleDateString('id-ID') : '-',
  },
  {
    field: 'type_customer.name',
    title: 'Type Customer',
    maxWidth: "100px",
    render: (row) => row.type_customer?.name ?? '-',
    filter: false,
  },
  {
    field: 'type_project.name',
    title: 'Type Project',
    minWidth: "100px",
    render: (row) => row.type_project?.name ?? '-',
    filter: false,
  },
  { field: 'project_name', title: 'Penawaran', filter: false, width: 150 },
  { field: 'pic_name', title: 'PIC Name', filter: false, width: 150 },
  { field: 'pic_phone', title: 'PIC Phone', filter: false, width: 150 },
  { field: 'pic_position', title: 'PIC Position', filter: false, width: 150 },
  {
    field: 'type_report.name',
    title: 'Type Report',
    minWidth: "100px",
    render: (row) => row.type_report?.name ?? '-',
    filter: false,
  },
  {
    field: 'report_notes',
    title: 'Report Notes',
    filter: false,
    minWidth: "200px",
    cellClass: "wrap-cell",
  },
  { field: 'equipment_needs', title: 'Kebutuhan Alat', filter: false, minWidth: "200px" },
  { field: 'items_purchase_order', title: 'Items Purchase Order', filter: false, width: 200 },
  {
    field: 'nominal_purchase_order',
    title: 'Estimated Nominal Purchase',
    filter: false,
    align: 'right',
    width: 200,
    render: (row) => {
      if (!row.nominal_purchase_order) return '-';
      const num = Number(row.nominal_purchase_order);
      return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        minimumFractionDigits: 0,
        maximumFractionDigits: 0,
      }).format(num);
    },
  },
]);

onMounted(fetchSalesReports);
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
