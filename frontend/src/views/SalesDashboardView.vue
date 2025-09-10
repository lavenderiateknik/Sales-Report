<template>
    <main>
        <div class="flex flex-col bg-[#10375C]/10 mx-2 my-2 rounded-2xl">
            <!-- Header -->
            <div class="flex flex-row items-center px-4 py-4 text-3xl text-slate-600">
                <span>Sales</span>
                <strong class="ml-2">Dashboard</strong>
            </div>

            <!-- Chart -->
            <div class="grid grid-cols-1 lg:grid-cols-2 mx-4 p-3 gap-4">
                <Chart class="pb-3" :chart-data="pieChartData" title1="Type " title2="Customer" />
                <Bar class="pb-3" :chart-data="barChartData" title1="Offering " title2="VS Preorder" />
            </div>

            <!-- Table 1 -->
            <div class="grid grid-cols-1 p-3 gap-4">
                <Tabel :rows-data="customersWithGrandTotal" :cols="colsData" title1="Recap"
                    title2="Visit, Follow up, Offering, PO, Negotiation & Grand Total" />
            </div>

            <!-- Table 2 & 3 -->
            <div class="grid grid-cols-1 lg:grid-cols-2 p-3 gap-4">
                <Tabel :rows-data="customers2WithTotal" :cols="colsData2" title1="Recap" title2="Penawaran & PO" />
                <Tabel :rows-data="nominalRowsDataFormatted" :cols="nominalCols" title1="Recap" title2="Nominal"
                    :pageable="true" />
            </div>

            <!-- Table + Chart -->
            <div class="grid grid-cols-1 lg:grid-cols-2 p-3 gap-4 justify-center items-end">
                <Tabel :rows-data="reportsWithTotal" :cols="colsReportType" title1="Recap" title2="Report Type"
                    :pageable="false" />
                <Chart :chart-data="reportsChartData" title1="Report " title2="Type" />
            </div>

        </div>
    </main>
</template>

<script setup>
import Chart from '@/components/Chart.vue';
import Bar from '@/components/Bar.vue';
import Tabel from '@/components/Tabel.vue';
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

// --- TABLE 1: Visit, Follow Up, dll. ---
const colsData = ref([
    { field: 'id', title: 'No.', isUnique: true, type: 'number', filter: false },
    { field: 'company', title: 'Company', filter: false },
    { field: 'visit', title: 'Visit', filter: false },
    { field: 'follow_up', title: 'Follow Up', filter: false },
    { field: 'penawaran', title: 'Penawaran', filter: false },
    { field: 'PO', title: 'PO', filter: false },
    { field: 'negotiation', title: 'Negotiation', filter: false },
    { field: 'grand_total', title: 'Grand Total', filter: false },
]);

const customers = ref([
    { id: 1, company: 'Unilever', visit: 4, follow_up: 10, penawaran: 3, PO: 5, negotiation: 3 },
    { id: 2, company: 'Hutama Karya', visit: 5, follow_up: 6, penawaran: 8, PO: 7, negotiation: 7 },
    { id: 3, company: 'Nindya Karya', visit: 10, follow_up: 7, penawaran: 8, PO: 3, negotiation: 4 },
    { id: 4, company: 'Pemkot Bali', visit: 13, follow_up: 5, penawaran: 8, PO: 9, negotiation: 10 },
]);

const customersWithGrandTotal = computed(() => {
    return customers.value.map(customer => ({
        ...customer,
        grand_total: customer.visit + customer.follow_up + customer.penawaran + customer.PO,
    }));
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

// --- TABLE 4: Report Type ---
const colsReportType = ref([

    { field: 'report_type', title: 'Report Type', filter: false },
    { field: 'total_type', title: 'Total', filter: false },
]);

const reports = ref([
    { report_type: 'VISIT', total_type: 4 },
    { report_type: 'FOLLOW UP', total_type: 7 },
    { report_type: 'OFFERING', total_type: 4 },
    { report_type: 'NEGOTIATE', total_type: 10 },
    { report_type: 'PO', total_type: 10 },
]);

const reportsWithTotal = computed(() => {
    const data = [...reports.value];
    const total = data.reduce((sum, current) => sum + current.total_type, 0);

    data.push({
        id: '',
        report_type: 'Total',
        total_type: total,
    });

    return data;
});

// Chart data diambil dari reportsWithTotal
const reportsChartData = computed(() => {
    const labels = reports.value.map(r => r.report_type);
    const data = reports.value.map(r => r.total_type);

    return {
        labels,
        datasets: [
            {
                label: 'Report Type',
                data,
                backgroundColor: ["#77CEFF", "#0079AF", "#123E6B", "#F3C623", "#10375C"],
            }
        ]
    };
});

</script>
