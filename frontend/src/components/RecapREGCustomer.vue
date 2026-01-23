<template>
  <div class="max-w-350 mx-auto p-6">
    <div class="bg-white border border-amber-300 rounded-2xl shadow-xl shadow-slate-400 overflow-hidden">

      <!-- HEADER -->
     <div class="flex justify-between items-center gap-3 px-3 py-3">
        <h1 class="text-2xl font-bold">Recap BCI Customers</h1>

        <div class="flex gap-2 items-center">
          <!-- TANGGAL MULAI -->
           Start
          <input
            type="date"
            v-model="startDate"
            class="rounded-xl border px-3 py-2 text-sm focus:ring-4 focus:ring-indigo-100"
          />

          <!-- TANGGAL AKHIR -->
           End
          <input
            type="date"
            v-model="endDate"
            class="rounded-xl border px-3 py-2 text-sm focus:ring-4 focus:ring-indigo-100"
          />

          <!-- SEARCH -->
          <input
            v-model="search"
            placeholder="Cari customer / project / tanggal / report type / nominal"
            class="rounded-xl border px-3 py-2 text-sm focus:ring-4 focus:ring-indigo-100 w-96"
          />
        </div>
      </div>

      <!-- TABLE -->
      <div class="overflow-x-auto">
        <table class="min-w-full text-sm table-fixed">
          <thead class="bg-slate-100 text-xs uppercase">
            <tr>
              <th class="px-4 py-3 text-center w-16">No</th>
              <th class="px-4 py-3 text-center w-32">Tanggal</th>
              <th class="px-4 py-3 text-center">Customer</th>
              <th class="px-4 py-3 text-center">Project</th>
              <th v-if="role === 1 || role === 2 || role === 3" class="px-4 py-3 text-center">
                Cabang
              </th>
              <th class="px-4 py-3 text-center w-40">Report Type</th>
              <th class="px-4 py-3 text-center w-40">Reported By</th>
              <th class="px-4 py-3 text-center w-40">Type Customer</th>
              <th class="px-4 py-3 text-center w-40">Nominal PO</th>
            </tr>
          </thead>

          <tbody>
            <tr
              v-for="(row, index) in paginatedData"
              :key="row.id"
              class="border-b hover:bg-slate-50"
            >
              <!-- NOMOR URUT DESC -->
              <td class="px-4 py-3 font-semibold">
                {{
                  filteredData.length
                  - ((page - 1) * pageSize)
                  - index
                }}
              </td>

              <td class="px-4 py-3">
                {{ row.date }}
              </td>

              <td class="px-4 py-3">
                <div class="font-semibold">
                  {{ row.customer_name }}
                </div>
                <div class="text-xs text-slate-400">
                  PIC: {{ row.pic_name }}
                </div>
              </td>
              <td v-if="role === 1 || role === 2 || role === 3" class="px-4 py-3">
                {{ row.user.branch.name}}
              </td>

              <td class="px-4 py-3">
                {{ row.project_name }}
              </td>

              <td class="px-4 py-3 text-center">
                <span
                  class="inline-flex  items-center px-2 py-1 rounded-full
                         text-xs font-semibold bg-indigo-100 text-indigo-700"
                >
                  {{ row.type_report?.name }}
                </span>
              </td>
              <td class="px-4 py-3 text-center">
                <span
                  class="inline-flex items-center px-2 py-1 rounded-full
                         text-xs font-semibold bg-indigo-100 text-indigo-700"
                >
                  {{ row.user.name }}
                </span>
              </td>
              <td class="px-4 py-3 text-center">
                <span
                  class="inline-flex items-center px-2 py-1 rounded-full
                         text-xs font-semibold bg-indigo-100 text-indigo-700"
                >
                  {{ row.type_customer.name }}
                </span>
              </td>

              <td class="px-4 py-3 text-right font-bold whitespace-nowrap">
                {{ formatCurrency(row.nominal_purchase_order) }}
              </td>
            </tr>

            <!-- EMPTY STATE -->
            <tr v-if="!filteredData.length">
              <td colspan="6" class="text-center py-8 text-slate-400">
                Tidak ada data BCI
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- PAGINATION -->
      <div class="flex justify-between items-center px-4 py-3 border-t">
        <div class="text-xs text-slate-500">
          Page {{ page }} / {{ totalPages }}
        </div>

        <div class="flex gap-2">
          <button
            @click="page--"
            :disabled="page === 1"
            class="px-3 py-1 rounded-lg border text-sm
                   disabled:opacity-40 disabled:cursor-not-allowed
                   hover:bg-slate-50"
          >
            Prev
          </button>

          <button
            @click="page++"
            :disabled="page === totalPages"
            class="px-3 py-1 rounded-lg border text-sm
                   disabled:opacity-40 disabled:cursor-not-allowed
                   hover:bg-slate-50"
          >
            Next
          </button>
        </div>
      </div>

    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, watch } from "vue";
import axios from "axios";

/* ================= ENV & AUTH ================= */
const apiBaseUrl = import.meta.env.VITE_API_BASE_URL;
const token = localStorage.getItem("api_token");
const role = Number(localStorage.getItem("role"));
const id = localStorage.getItem("id");
const branch = localStorage.getItem("branch");

/* ================= STATE ================= */
const loading = ref(false);
const reports = ref([]);
const search = ref("");
const startDate = ref(""); // yyyy-mm-dd
const endDate = ref("");   // yyyy-mm-dd
const page = ref(1);
const pageSize = 10;

/* ================= FETCH ================= */
const fetchSalesReports = async () => {
  if (!apiBaseUrl) {
    console.error("VITE_API_BASE_URL belum diset");
    return;
  }
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
        Authorization: `Bearer ${token}`,
        Accept: "application/json",
      },
    });

    // 🔒 Pastikan data berupa ARRAY
    const raw = res.data?.data ?? res.data;

    reports.value = Array.isArray(raw)
      ? raw
      : Object.values(raw || []);

  } catch (err) {
    console.error("Gagal ambil sales report:", err);
    reports.value = [];
  } finally {
    loading.value = false;
  }
};

onMounted(fetchSalesReports);

/* ================= FILTER + SORT ================= */
const filteredData = computed(() => {
  if (!Array.isArray(reports.value)) return [];

  return reports.value
    // ✅ HANYA BCI
    .filter((r) => r.type_customer?.name === "REG")

    // 📅 FILTER RENTANG TANGGAL
    .filter((r) => {
      if (!r.date) return false;

      const rowDate = new Date(r.date);

      if (startDate.value) {
        const start = new Date(startDate.value);
        if (rowDate < start) return false;
      }

      if (endDate.value) {
        const end = new Date(endDate.value);
        end.setHours(23, 59, 59, 999); // inklusif sampai akhir hari
        if (rowDate > end) return false;
      }

      return true;
    })

    // 🔍 SEARCH TEXT
    .filter((r) =>
      (
        (r.customer_name ?? "") +
        (r.project_name ?? "") +
        (r.type_report?.name ?? "") +
        (r.user.name ?? "") +
        (r.date ?? "") +
        (r.nominal_purchase_order ?? "")
      )
        .toLowerCase()
        .includes(search.value.toLowerCase())
    )

    // 🔽 TERBARU DI ATAS
    .sort((a, b) => new Date(b.date) - new Date(a.date));
});


/* ================= PAGINATION ================= */
const totalPages = computed(() =>
  Math.max(1, Math.ceil(filteredData.value.length / pageSize))
);

const paginatedData = computed(() => {
  const start = (page.value - 1) * pageSize;
  return filteredData.value.slice(start, start + pageSize);
});

/* ================= RESET PAGE SAAT SEARCH ================= */
watch([search, startDate, endDate], () => {
  page.value = 1;
});

/* ================= FORMAT ================= */
const formatCurrency = (val) => {
  if (!val || Number(val) === 0) return "-";
  return new Intl.NumberFormat("id-ID", {
    style: "currency",
    currency: "IDR",
    maximumFractionDigits: 0,
  }).format(val);
};
</script>

