<template>
  <div class="bg-[#10375C]/10 mx-2 my-2 rounded-2xl">
    <div v-if="isAllowed">
      <!-- ===== HEADER ===== -->
      <div class="flex flex-row items-center px-4 py-4 text-3xl text-slate-600">
        <span>Customer</span>
        <strong class="ml-2">History</strong>
      </div>

      <!-- ===== SEARCH ===== -->
      <div class="px-4 mb-4">
        <input
          v-model="searchQuery"
          type="text"
          placeholder="Search customer, project, PIC..."
          class="w-full md:w-1/2 px-4 py-2 border rounded shadow-sm focus:outline-none focus:ring focus:border-blue-300"
        />
      </div>

      <!-- ===== TABLE CUSTOMER PROJECT ===== -->
      <div class="overflow-x-auto px-4">
        <table class="min-w-full border border-gray-200 bg-white">
          <thead class="bg-gray-100">
            <tr>
              <th class="px-4 py-2 border">NO.</th>
              <th class="px-4 py-2 border">Customer</th>
              <th class="px-4 py-2 border">PIC</th>
              <th class="px-4 py-2 border">Contact</th>
              <th class="px-4 py-2 border">Project</th>
              <th class="px-4 py-2 border">History</th>
            </tr>
          </thead>
          <tbody>
            <tr
              v-for="customer in customersProject"
              :key="customer.no"
              class="border-t"
            >
              <td class="px-4 py-2 border">{{ customer.no }}</td>
              <td class="px-4 py-2 border">{{ customer.customer_name }}</td>
              <td class="px-4 py-2 border">{{ customer.pic_name }}</td>
              <td class="px-4 py-2 border">{{ customer.pic_phone }}</td>
              <td class="px-4 py-2 border">{{ customer.project_name }}</td>
              <td class="px-4 py-2 border">
                <button
                  class="px-2 py-1 bg-blue-600 text-white rounded hover:bg-blue-700"
                  @click="openHistory(customer)"
                >
                  History
                </button>
              </td>
            </tr>
            <tr v-if="customersProject.length === 0">
              <td colspan="6" class="text-center py-4">No customers found.</td>
            </tr>
          </tbody>
        </table>
      </div>
      
      <!-- ===== ROWS PER PAGE ===== -->
      <!-- <div class="flex justify-end items-center px-4 mt-2">
        <label class="mr-2 text-sm text-gray-600">Rows:</label>
        <select
          v-model="perPage"
          class="border rounded px-2 py-1 text-sm"
        >
          <option :value="5">5</option>
          <option :value="10">10</option>
          <option :value="25">25</option>
          <option :value="50">50</option>
        </select>
      </div> -->

      <!-- ===== PAGINATION ===== -->
      <div class="flex justify-between items-center mt-4 px-4">
        <button
          class="px-4 py-2 bg-gray-200 rounded hover:bg-gray-300"
          :disabled="currentPage === 1"
          @click="currentPage--"
        >
          Previous
        </button>
        <span>Page {{ currentPage }} of {{ totalPages }}</span>
        <button
          class="px-4 py-2 bg-gray-200 rounded hover:bg-gray-300"
          :disabled="currentPage === totalPages"
          @click="currentPage++"
        >
          Next
        </button>
      </div>

      <!-- ===== MODAL HISTORY ===== -->
      <div
        v-if="showModal"
        class="fixed inset-0 bg-black/80 flex items-center justify-center z-50"
      >
        <div class="bg-white rounded-xl shadow-lg w-11/12 md:w-3/4 p-6 max-h-[70vh] overflow-y-auto">
          <h2 class="text-xl font-bold text-slate-700 mb-4">
            History - {{ selectedCustomer?.customer_name }}
          </h2>

          <div v-if="selectedCustomer?.history?.length">
            <div class="overflow-x-auto">
              <table class="min-w-full border border-gray-200">
                <thead class="bg-gray-100">
                  <tr>
                    <th class="px-4 py-2 border">No.</th>
                    <th class="px-4 py-2 border">Tanggal Report</th>
                    <th class="px-4 py-2 border">Type Report</th>
                    <th class="px-4 py-2 border">Report Notes</th>
                    <th class="px-4 py-2 border">Report By</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="(report, idx) in selectedCustomer.history" :key="idx" class="border-t">
                    <td class="px-4 py-2 border">{{ idx + 1 }}</td>
                    <td class="px-4 py-2 border">{{ report.date }}</td>
                    <td class="px-4 py-2 border">{{ report.type_report_name }}</td>
                    <td class="px-4 py-2 border break-all">{{ report.report_notes }}</td>
                    <td class="px-4 py-2 border">{{ report.user_name }}</td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
          <div v-else class="text-slate-500 italic">
            No history available for your login.
          </div>

          <div class="flex justify-end mt-6">
            <button
              class="px-4 py-2 bg-gray-500 text-white rounded hover:bg-gray-600"
              @click="showModal = false"
            >
              Close
            </button>
          </div>
        </div>
      </div>
    </div>
    <div v-else class="p-6 text-center text-red-600">
      🚫 Anda tidak memiliki akses ke halaman ini
    </div>
  </div>
</template>

<script setup>
import axios from "axios";
import { ref, computed, onMounted, watch } from "vue";
import currency from "currency.js";

/* ===========================
   Helpers
=========================== */
function formatCurrency(value) {
  if (value === null || value === undefined || value === "") return "-";
  const num = Number(value);
  if (!Number.isFinite(num)) return "-";
  return currency(num, {
    symbol: "Rp ",
    separator: ".",
    decimal: ",",
    precision: 0
  }).format();
}

function formatDate(value) {
  if (!value) return "-";
  const d = new Date(value);
  if (Number.isNaN(d.getTime())) return String(value);
  return d.toLocaleDateString("id-ID", {
    day: "2-digit",
    month: "long",
    year: "numeric"
  });
}

/* ===========================
   State
=========================== */
const customersProject = ref([]);
const loading = ref(false);

const token = localStorage.getItem("api_token");
const id = localStorage.getItem("id");
const role = parseInt(localStorage.getItem("role"));

const branch = localStorage.getItem("branch");
const apiBaseUrl = import.meta.env.VITE_API_BASE_URL;

const isAllowed = computed(() => role <= 3);

/* ===========================
   Modal
=========================== */
const showModal = ref(false);
const selectedCustomer = ref(null);

/* ===========================
   Search & Pagination
=========================== */
const searchQuery = ref("");
const currentPage = ref(1);
const perPage = ref(10);

const totalPages = ref(1);
const totalData = ref(0);

/* ===========================
   Fetch API
=========================== */
const fetchSalesReports = async () => {
  let url = "";

  if (role === 7) {
    url = `${apiBaseUrl}/api/salesreports/${id}`;
  } else if ([6, 5, 4].includes(role)) {
    url = `${apiBaseUrl}/api/branchsalesreports/${branch}`;
  } else {
    url = `${apiBaseUrl}/api/allsalesreports`;
  }

  loading.value = true;

  try {
    const res = await axios.get(url, {
      headers: {
        Authorization: `Bearer ${token}`
      },
      params: {
        page: currentPage.value,
        per_page: perPage.value,
        search: searchQuery.value
      }
    });

    const data = res.data.data || [];

    totalPages.value = res.data.last_page;
    totalData.value = res.data.total;

    const grouped = {};

    data.forEach((item) => {
      const key = item.customer_name ?? "Unknown";

      if (!grouped[key]) grouped[key] = [];

      grouped[key].push({
        date: formatDate(item.date),
        type_report_name: item.type_report?.name ?? "-",
        report_notes: item.report_notes ?? "",
        project_name: item.project_name ?? "-",
        pic_name: item.pic_name ?? "-",
        pic_phone: item.pic_phone ?? "-",
        nominal_purchase_order: formatCurrency(item.nominal_purchase_order),
        user_name: item.user?.name ?? "-"
      });
    });

    customersProject.value = Object.keys(grouped).map((customerName, idx) => {
      const history = grouped[customerName];
      const first = history[0];

      return {
        no: (currentPage.value - 1) * perPage.value + idx + 1,
        customer_name: customerName,
        project_name: first.project_name,
        pic_name: first.pic_name,
        pic_phone: first.pic_phone,
        nominal_purchase_order: first.nominal_purchase_order,
        history: history
      };
    });

  } catch (err) {
    console.error("❌ Fetch error:", err);
  } finally {
    loading.value = false;
  }
};

/* ===========================
   Actions
=========================== */
const openHistory = (row) => {
  selectedCustomer.value = row;
  showModal.value = true;
};

/* ===========================
   Watchers
=========================== */

watch([currentPage, perPage], () => {
  fetchSalesReports();
});

watch(searchQuery, () => {
  currentPage.value = 1;
  fetchSalesReports();
});

/* ===========================
   Lifecycle
=========================== */

onMounted(() => {
  fetchSalesReports();
});
</script>

<style scoped>
table {
  border-collapse: collapse;
  width: 100%;
}
th, td {
  border: 1px solid #ddd;
}
</style>
