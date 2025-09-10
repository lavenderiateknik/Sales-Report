<template>
  <div>
    <div class="flex flex-col bg-[#10375C]/10 mx-2 my-2 rounded-2xl">
      <div class="flex flex-row items-center px-4 py-4 text-3xl text-slate-600">
          <span>Customer</span>
          <strong class="ml-2">Dashboard</strong>
      </div>
      <Tabel 
          :rows-data="customerProject" 
          :cols="colsCustomerProject" 
          title1="Recap" 
          title2="Customer Project" 
      />
      <Tabel 
          :rows-data="customerNotes" 
          :cols="colsCustomerNotes" 
          title1="Recap" 
          title2="Customer Notes" 
      />
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue';
import Tabel from '@/components/Tabel.vue';

// ✅ Definisikan data kolom di parent
const colsCustomerProject = ref([
  { field: 'id', title: 'NO.', isUnique: true, type: 'number', filter:false },
  { field: 'customer', title: 'Customer', filter:false },
  { field: 'PIC', title: 'PIC', filter:false },
  { field: 'project', title: 'Project',filter:false },
  { field: 'action', title: 'Action', filter: false },
]);

// Data di parent
const customerProject = [
  {
    id: 1,
    customer: 'ABIBRAYA',
    PIC: 'Santoso',
    project: 'Bendungan',
    date: "2025-11-09",
    action: "Edit | View",
  },
  {
    id: 2,
    customer: 'ADHI KARYA',
    PIC: 'Wijaya',
    project: 'Perumahan',
    date: "2025-04-08",
    action: false,
  },
  {
    id: 3,
    customer: 'WIJAYA KARYA',
    PIC: 'Prasetyo',
    project: 'Ruko',
    date: "2025-03-13",
    action: "Edit | View",
  },
];

const colsCustomerNotes = ref([
  { field: 'date', title: 'Date', type: 'date' },
  { field: 'report_type', title: 'Report type', filter:false },
  { field: 'report_notes', title: 'Report Notes', filter:false },
  { field: 'action', title: 'Action', filter: false },
]);

const customerNotes = [
  {
    date: "2025-11-09",
    report_type: 'follow up',
    report_notes: 'Order Menyusul',
    action: "Edit | View",
  },
  {
    date: "2025-04-08",
    report_type: 'PO',
    report_notes: 'Order Menyusul',
    action: "Edit | View",
  },
  {
    date: "2025-03-13",
    report_type: 'follow up',
    report_notes: 'Order Menyusul',
    action: "Edit | View",
  },
];


</script>