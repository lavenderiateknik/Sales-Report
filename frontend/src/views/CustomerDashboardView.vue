<template>
  <div>
    <Transition appear enter-active-class="transition-transform duration-1000 ease-out" enter-from-class="translate-x-10 opacity-0" enter-to-class="translate-x-0 opacity-100">
      <div class="flex flex-col bg-[#10375C]/10 mx-2 my-2 rounded-2xl">
        <div class="flex flex-row items-center px-4 py-4 text-3xl text-slate-600">
            <span>Customer</span>
            <strong class="ml-2">Dashboard</strong>
        </div>
        <Tabel 
            :rows-data="customersProject" 
            :cols="colsCustomerProject" 
            title1="Recap" 
            title2="Customer Project" 
        />
        <Tabel 
            :rows-data="sortedCustomerNotes" 
            :cols="colsCustomerNotes" 
            title1="Recap" 
            title2="Customer Notes" 
        />
      </div>
    </Transition>
  </div>
</template>

<script setup>
import Tabel from '@/components/Tabel.vue';
import axios from 'axios';
import { ref, computed, onMounted } from 'vue';
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



const customersProject = ref([]);

const loading = ref(false);
const url = ref('');
const token = localStorage.getItem('api_token');
const id = localStorage.getItem('id');
const role = parseInt(localStorage.getItem('role'));
const branch = localStorage.getItem('branch');
const apiBaseUrl = import.meta.env.VITE_API_BASE_URL;



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
    
    customersProject.value = data.map((item, idx) => ({
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

// ✅ Definisikan data kolom di parent
const colsCustomerProject = ref([
  { field: 'no', title: 'NO.', isUnique: true, type: 'number', filter:false },
  { field: 'customer_name', title: 'Customer', filter:false },
  { field: 'pic_name', title: 'PIC', filter:false },
  { field: 'pic_phone', title: 'Contact', filter:false },
  { field: 'project_name', title: 'Project',filter:false },
  
]);

const colsCustomerNotes = ref([
  { field: 'no', title: 'NO.', isUnique: true, type: 'number', filter:false },
  { field: 'date', title: 'Date' },
  { field: 'type_report.name', title: 'Type Report'},
  { field: 'report_notes', title: 'Report Notes', filter:false },
  
]);

const sortedCustomerNotes = computed(() => {
  // pastikan type_report ada sebelum akses name
  return customersProject.value.slice().sort((a, b) => {
    const nameA = a.type_report?.name ?? '';
    const nameB = b.type_report?.name ?? '';
    return nameA.localeCompare(nameB); // urut ascending
  });
});

onMounted(() => {
  fetchSalesReports();

});
</script>