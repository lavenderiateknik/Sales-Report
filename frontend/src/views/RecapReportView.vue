<template>
    <div>
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
Reference or State
=========================== */
const url = ref('');
const customers = ref([]);
const loading = ref(false);

/* ===========================
local storage variable
=========================== */
const token = localStorage.getItem('api_token');
const id = localStorage.getItem('id');
const branch = localStorage.getItem('branch');
const role = parseInt(localStorage.getItem('role'));

/* ===========================
Url base
=========================== */
const apiBaseUrl = import.meta.env.VITE_API_BASE_URL;

/* ===========================
Columns data
=========================== */
const colsData = ref([
  { field: 'no', title: 'No', align: 'center', minWidth: '60px', filter: false },
  {
    field: 'date',
    title: 'Date',
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

/* ===========================
Function
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

onMounted(() => {
  fetchSalesReports();

  // auto-refresh setiap 5 menit
  setInterval(() => {
    fetchSalesReports();
  }, 5 * 60 * 1000);

  
});
</script>

<style lang="scss" scoped>

</style>