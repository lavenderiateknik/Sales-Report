<template>
  <div class="bg-[#10375C]/10 mx-2 my-2 rounded-2xl">
    <!-- SEARCH BAR -->
    <div class="flex items-center justify-end gap-2 mb-3 py-2 px-3">
      <span>Search Daily Visit</span>
      <input
        v-model="search"
        
        type="text"
        placeholder="Search data..."
        class=" bg-white border rounded-lg px-3 py-2 w-full md:w-64 focus:outline-none focus:ring-2 focus:ring-blue-400"
      />
    </div>
    <Tabel
      :rows-data="filteredCustomers"
      :cols="colsData"
      title1="Daily"
      title2="Visit"
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
State
=========================== */
const url = ref('');
const allCustomers = ref([]);   // 🔹 data asli
const search = ref('');
const loading = ref(false);

/* ===========================
Local storage
=========================== */
const token = localStorage.getItem('api_token');
const id = localStorage.getItem('id');
const branch = localStorage.getItem('branch');
const role = parseInt(localStorage.getItem('role'));

/* ===========================
Base URL
=========================== */
const apiBaseUrl = import.meta.env.VITE_API_BASE_URL;

/* ===========================
Columns
=========================== */
const colsData = ref([
  { field: 'no', title: 'No', align: 'center', filter: false },
  {
    field: 'date',
    title: 'Date',
    type: 'date',
    minWidth: '150px',
    align: 'center',
    render: (value) => (value ? formatDate(value) : '-'),
  },
  { field: 'customer_name', title: 'Name Customer', width: 200, render: (value) => value ?? '-' },
  { field: 'user.name', title: 'Sales Name', render: (value, row) => value ?? row?.user?.name ?? '-' },
  { field: 'type_customer.name', title: 'Type Customer', render: (value, row) => value ?? row?.type_customer?.name ?? '-' },
  { field: 'type_project', title: 'Type Project', render: (value, row) => value ?? row?.type_project?.name ?? '-' },
  { field: 'project_name', title: 'Project Name', render: (value) => value ?? '-' },
  { field: 'pic_name', title: 'PIC Name', filter: false, render: (value) => value ?? '-' },
  { field: 'pic_phone', title: 'PIC Phone', filter: false, render: (value) => value ?? '-' },
  { field: 'pic_position', title: 'PIC Position', filter: false, render: (value) => value ?? '-' },
  { field: 'type_report.name', title: 'Type Report', filter: false, render: (value, row) => value ?? row?.type_report?.name ?? '-' },
  { field: 'report_notes', title: 'Report Notes', filter: false, cellClass: 'wrap-cell', width: 200, render: (value) => value ?? '-' },
  { field: 'equipment_needs', width: 250, title: 'Equipments Needs', render: (value) => value ?? '-' },
  { field: 'items_purchase_order', width: 250, title: 'Items Purchase Order', filter: false, render: (value) => value ?? '-' },
  {
    field: 'nominal_purchase_order',
    title: 'Estimated Nominal Purchase',
    width: 150,
    align: 'right',
    filter: false,
    cell: (row) => formatCurrency(row.nominal_purchase_order ?? '-')
  }
]);

/* ===========================
FETCH
=========================== */
const fetchSalesReports = async () => {
  if (role === 7) {
    url.value = `${apiBaseUrl}/api/salesreports/${id}`;
  } else if ([6, 5, 4].includes(role)) {
    url.value = `${apiBaseUrl}/api/branchsalesreports/${branch}`;
  } else {
    url.value = `${apiBaseUrl}/api/allsalesreports`;
  }

  loading.value = true;
  try {
    const res = await axios.get(url.value, {
      headers: { Authorization: `Bearer ${token}` }
    });

    const data = res.data.data ?? res.data;

    allCustomers.value = data.map((item, idx) => ({
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

/* ===========================
SEARCH FILTER
=========================== */
const filteredCustomers = computed(() => {
  if (!search.value) return allCustomers.value;

  const key = search.value.toLowerCase();

  return allCustomers.value.filter(r =>
    r.customer_name?.toLowerCase().includes(key) ||
    r.user?.name?.toLowerCase().includes(key) ||
    r.type_customer?.name?.toLowerCase().includes(key) ||
    r.type_project?.name?.toLowerCase().includes(key) ||
    r.project_name?.toLowerCase().includes(key) ||
    r.pic_name?.toLowerCase().includes(key) ||
    r.type_report?.name?.toLowerCase().includes(key)
  );
});

/* ===========================
Lifecycle
=========================== */
onMounted(() => {
  fetchSalesReports();

  // auto-refresh setiap 5 menit
  setInterval(() => {
    fetchSalesReports();
  }, 5 * 60 * 1000);
});
</script>

<style scoped>
</style>
