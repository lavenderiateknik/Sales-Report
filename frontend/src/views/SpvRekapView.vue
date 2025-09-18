<template>
  <div>
   
      <div class="flex flex-col bg-[#10375C]/10 mx-2 my-2 rounded-2xl">
        <!-- Header -->
        <div class="flex flex-row items-center px-4 py-4 text-3xl text-slate-600">
          <span>Supervisor</span>
          <strong class="ml-2">Rekap</strong>
        </div>
        <!-- Chart -->
          <Transition appear enter-active-class="transition-transform duration-1000 ease-out" enter-from-class="translate-y-30 opacity-0" enter-to-class="translate-x-0 opacity-100">
            <div class="grid grid-cols-1 lg:grid-cols-2 mx-4 p-3 gap-4">
              <Chart class="pb-3" :chart-data="pieChartData" title1="Type " title2="Customer" />
              <Bar class="pb-3" :chart-data="barChartData" title1="Offering " title2="VS Preorder" />
            </div>
          </Transition>
        <div>
          <Tabel
            :rows-data="customersWithTotal"
            :cols="colsData"
            title1="Customer"
            title2="Rekap By Date"
          />
        </div>
         <!-- table 2 & 3 -->
        <div class="grid grid-cols-1 lg:grid-cols-2 p-3 gap-4">
          <Tabel :rows-data="customers2WithTotal" :cols="colsData2" title1="Recap" title2="Penawaran & PO" :pageable="false" />
          <Tabel :rows-data="nominalRowsDataFormatted" :cols="nominalCols" title1="Recap" title2="Nominal"
            :pageable="false" />
        </div>
        <div>
          <Tabel
            :rows-data="recapReportByCustomersWithTotal"
            :cols="colsRecapReportByCustomer"
            title1="Recap Report"
            title2="By Customer Name"
          />
        </div>
         <div>
          <Tabel
            :rows-data="recapCustomerNeeds"
            :cols="colsRecapCustomerNeeds"
            title1="Recap"
            title2="Customer Needs"
          />
        </div>
      </div>   
    
  </div>
</template>

<script setup>
import Tabel from '@/components/Tabel.vue';
import Chart from '@/components/Chart.vue';
import Bar from '@/components/Bar.vue';
import { ref, computed } from 'vue';

// --- CHART DATA ---
const pieChartData = ref({
    labels: ["BCI", "REG", "OTHER"],
    datasets: [
        {
            data: [30, 40, 60],
            backgroundColor: ["#77CEFF", "#0079AF", "#123E6B"],
        },
    ],
});

const barChartData = ref({
    labels: ["Januari", "Februari", "Maret", "April"],
    datasets: [
        {
            label: "Penawaran",
            data: [16, 25, 35, 35],
            backgroundColor: "#F3C623",
        },
        {
            label: "Preorder (PO)",
            data: [8, 18, 10, 30],
            backgroundColor: "#10375C",
        },
    ],
});


// Definisikan data kolom di parent
const colsData = ref([
  { field: 'id', title: 'No.', isUnique: true, type: 'number', filter: false },
  { field: 'tanggal', title: 'Tanggal', type: 'date' },
  { field: 'visit', title: 'Visit', filter: false },
  { field: 'follow_up', title: 'Follow Up', filter: false },
  { field: 'penawaran', title: 'Penawaran', filter: false },
  { field: 'PO', title: 'PO', filter: false },
  { field: 'negotiation', title: 'Negotiation', filter: false },
  { field: 'grand_total', title: 'Grand Total', filter: false },
]);

// Data di parent
const customers = ref([
  {
    id: 1,
    tanggal: '11/09/2025',
    visit: 0,
    follow_up: 10,
    penawaran: 3,
    PO: 5,
    negotiation: 3,
  },
  {
    id: 2,
    tanggal: '03/09/2025',
    visit: 5,
    follow_up: 4,
    penawaran: 0,
    PO: 7,
    negotiation: 7,
  },
  {
    id: 3,
    tanggal: '05/08/2025',
    visit: 10,
    follow_up: 0,
    penawaran: 8,
    PO: 3,
    negotiation: 4,
  },
  {
    id: 4,
    tanggal: '04/04/2025',
    visit: 13,
    follow_up: 5,
    penawaran: 8,
    PO: 9,
    negotiation: 0,
  },
]);

// ✅ Menggunakan computed property untuk menghitung grand_total dan menambahkan baris total.
const customersWithTotal = computed(() => {
  // Tambahkan grand_total ke setiap baris data
  const data = customers.value.map(customer => ({
    ...customer,
    grand_total: customer.visit + customer.follow_up + customer.penawaran + customer.PO,
  }));

  // Hitung total untuk setiap kolom numerik
  const totals = data.reduce((acc, current) => {
    acc.visit += current.visit;
    acc.follow_up += current.follow_up;
    acc.penawaran += current.penawaran;
    acc.PO += current.PO;
    acc.negotiation += current.negotiation;
    acc.grand_total += current.grand_total;
    return acc;
  }, {
    visit: 0,
    follow_up: 0,
    penawaran: 0,
    PO: 0,
    negotiation: 0,
    grand_total: 0
  });

  // Buat baris total
  data.push({
    id: 'Total',
    tanggal: 'Total', // Ubah 'tanggal' menjadi 'Total'
    visit: totals.visit,
    follow_up: totals.follow_up,
    penawaran: totals.penawaran,
    PO: totals.PO,
    negotiation: totals.negotiation,
    grand_total: totals.grand_total,
  });

  return data;
});

// --- TABLE 2: Penawaran & PO ---
const colsData2 = ref([
    { field: 'id', title: 'No.', isUnique: true, type: 'number', filter: false },
    { field: 'month', title: 'Month', filter: false },
    { field: 'penawaran', title: 'Penawaran', filter: false },
    { field: 'PO', title: 'PO', filter: false },
    { field: 'grand_total', title: 'Grand Total', filter: false },
]);

const customers2 = ref([
    { id: 1, month: 'Januari', penawaran: 3, PO: 5 },
    { id: 2, month: 'Februari', penawaran: 8, PO: 7 },
    { id: 3, month: 'Maret', penawaran: 10, PO: 7 },
]);

const customers2WithTotal = computed(() => {
    return customers2.value.map(customer => ({
        ...customer,
        grand_total: customer.penawaran + customer.PO,
    }));
});

// --- TABLE 3: Nominal ---
const nominalCols = ref([
    { field: 'no', title: 'No.', isUnique: true, type: 'number', filter: false },
    { field: 'month', title: 'Month', filter: false },
    { field: 'nominal', title: 'Nominal', filter: false },
]);

// Data asli
const nominalRowsData = ref([
    { no: 1, month: 'Januari', nominal: 323896580 },
    { no: 2, month: 'Februari', nominal: 256785420 },
    { no: 3, month: 'Maret', nominal: 673568542 },
]);

// ✅ Format nominal jadi Rupiah sebelum dikirim ke Tabel
const nominalRowsDataFormatted = computed(() =>
    nominalRowsData.value.map(item => ({
        ...item,
        nominal: new Intl.NumberFormat('id-ID', {
            style: 'currency',
            currency: 'IDR',
            minimumFractionDigits: 0
        }).format(item.nominal)
    }))
);

// --- TABLE 4: Recap Visit By Customer ---
const colsRecapReportByCustomer = ref([
    { field: 'customer', title: 'Customer', filter: false },
    { field: 'project_name', title: 'Project Name', filter: false },
    { field: 'visit', title: 'Visit', filter: false },
    { field: 'follow', title: 'Follow', filter: false },
    { field: 'negosiasi', title: 'Negosiasi', filter: false },
    { field: 'penawaran', title: 'Penawaran', filter: false },
    { field: 'po', title: 'PO', filter: false },
    { field: 'grand_total', title: 'Grand Total', filter: false },
]);

const recapReportByCustomers = ref([
    { customer: 'ABIBRAYA', project_name: "Jembatan Cianjuran", visit: "0", follow: "3", negosiasi: "7", penawaran: "11", po: "17" },
    { customer: 'Nindya Karya', project_name: "Gedung MA IKN", visit: "0", follow: "4", negosiasi: "8", penawaran: "13", po: "11" },
    { customer: 'Bakri Group', project_name: "Universitas", visit: "3", follow: "1", negosiasi: "9", penawaran: "9", po: "5" },
]);

const recapReportByCustomersWithTotal = computed(() => {
  // Tambahkan grand_total ke setiap baris data
  const data = recapReportByCustomers.value.map(item => {
    const visit = Number(item.visit) || 0;
    const follow = Number(item.follow) || 0;
    const negosiasi = Number(item.negosiasi) || 0;
    const penawaran = Number(item.penawaran) || 0;
    const po = Number(item.po) || 0;

    return {
      ...item,
      grand_total: visit + follow + negosiasi + penawaran + po,
    };
  });

  // Hitung total untuk setiap kolom numerik
  const totals = data.reduce((acc, current) => {
    acc.visit += Number(current.visit) || 0;
    acc.follow += Number(current.follow) || 0;
    acc.negosiasi += Number(current.negosiasi) || 0;
    acc.penawaran += Number(current.penawaran) || 0;
    acc.po += Number(current.po) || 0;
    acc.grand_total += Number(current.grand_total) || 0;
    return acc;
  }, {
    visit: 0,
    follow: 0,
    negosiasi: 0,
    penawaran: 0,
    po: 0,
    grand_total: 0
  });

  // Buat baris total
  data.push({
    customer: 'Total',
    project_name: '',
    visit: totals.visit,
    follow: totals.follow,
    negosiasi: totals.negosiasi,
    penawaran: totals.penawaran,
    po: totals.po,
    grand_total: totals.grand_total,
  });

  return data;
});

// --- TABLE 4: Recap Customer Needs ---
const colsRecapCustomerNeeds = ref([
  { field: 'customer', title: 'Customer', filter: false },
  { field: 'project_name', title: 'Project Name', filter: false },
  { field: 'date', title: 'Date', filter: false },
  { field: 'report_type', title: 'Report Type', filter: false },
  { field: 'needs', title: 'Needs', filter: false },
]);

const recapCustomerNeeds = ref([
  { customer: 'ABIBRAYA', project_name: "Jembatan Cianjuran", visit: "0", date: "01/09/2025", report_type: "Negosiasi", needs: "Wacker"},
  { customer: 'NINDYA KARYA', project_name: "RS HUSADA TERNATE", visit: "0", date: "08/09/2025", report_type: "Follow up", needs: "Vibrator IE58"},
  { customer: 'Bakrie Group', project_name: "Universitas", visit: "0", date: "11/08/2025", report_type: "negotiation", needs: "Universal Worm Pump Putzmeister"},
]);

</script>