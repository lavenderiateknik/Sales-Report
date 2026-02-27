<template>
  <div
    class="flex flex-col bg-[#10375C]/10 mx-2 my-2 px-3 py-3 rounded-2xl gap-1"
  >
    <!-- NAV -->
    <RouterLink to="/optionreport" class="flex gap-2 place-items-center">
      <ArrowLeftFromLineIcon class="w-4" /> Back
    </RouterLink>

    <RouterLink
      to="/checkin"
      class="bg-blue-400 hover:bg-blue-700 w-36 px-2 py-2 text-center rounded-md shadow-2xl cursor-pointer text-slate-900 hover:text-white"
    >
      Checkin
    </RouterLink>

    <div class="py-8">
      <h2 class="text-2xl font-semibold leading-tight">Visit History</h2>

      <div class="-mx-4 sm:-mx-8 px-4 sm:px-8 py-4 overflow-x-auto">
        <div
          class="inline-block min-w-full shadow rounded-lg overflow-hidden relative"
        >
          <!-- LOADING -->
          <div
            v-if="loading"
            class="absolute inset-0 bg-white/70 backdrop-blur-sm flex flex-col items-center justify-center z-10"
          >
            <div
              class="w-10 h-10 border-4 border-blue-500 border-t-transparent rounded-full animate-spin"
            ></div>
            <p class="mt-2 text-sm text-gray-600">Loading data...</p>
          </div>

          <!-- SEARCH -->
          <div
            class="flex items-center justify-between px-4 py-3 bg-white border-b"
          >
            <input
              v-model="search"
              type="text"
              placeholder="Cari perusahaan / proyek..."
              class="border px-3 py-2 rounded w-64 text-sm"
            />

            <div class="text-sm text-gray-600">
              Page {{ currentPage }} / {{ totalPages || 1 }}
            </div>
          </div>

          <table class="min-w-full leading-normal">
            <thead>
              <tr>
                <th class="px-5 py-3 border-b-2 bg-gray-100 text-xs">Nama Perusahaan</th>
                <th class="px-5 py-3 border-b-2 bg-gray-100 text-xs">Nama Proyek</th>
                <th class="px-5 py-3 border-b-2 bg-gray-100 text-xs">Tanggal</th>
                <th class="px-5 py-3 border-b-2 bg-gray-100 text-xs">Checkin</th>
                <th class="px-5 py-3 border-b-2 bg-gray-100 text-xs">Checkout</th>
                <th class="px-5 py-3 border-b-2 bg-gray-100 text-xs">Report By</th>
                <th class="px-5 py-3 border-b-2 bg-gray-100 text-xs">Status</th>
                <th class="px-5 py-3 border-b-2 bg-gray-100 text-xs">Aksi</th>
              </tr>
            </thead>

            <tbody>
              <tr v-for="row in filteredVisits" :key="row.id">
                <td class="px-5 py-5 border-b bg-white text-sm">
                  {{ row.customer_name }}
                </td>

                <td class="px-5 py-5 border-b bg-white text-sm">
                  {{ row.project_name }}
                </td>

                <td class="px-5 py-5 border-b bg-white text-sm">
                  {{ formatDate(row.date) }}
                </td>

                <td class="px-5 py-5 border-b bg-white text-sm">
                  {{ row.check_in }}
                </td>

                <td class="px-5 py-5 border-b bg-white text-sm">
                  {{ row.check_out ?? "-" }}
                </td>

                <td class="px-5 py-5 border-b bg-white text-sm">
                  {{ row.user.name }}
                </td>

                <td class="px-5 py-5 border-b bg-white text-sm">
                  <span
                    class="relative inline-block px-3 py-1 font-semibold text-white"
                  >
                    <span
                      class="absolute inset-0 rounded-full opacity-50"
                      :class="
                        isExpired(row)
                          ? 'bg-gray-600'
                          : row.check_out
                          ? 'bg-green-600'
                          : 'bg-red-600'
                      "
                    ></span>

                    <span class="relative">
                      <template v-if="row.check_out">
                        Sudah Checkout
                      </template>

                      <template v-else-if="isExpired(row)">
                        Expired
                      </template>

                      <template v-else>
                        Belum Checkout
                      </template>
                    </span>
                  </span>
                </td>

                <td class="px-5 py-5 border-b bg-white text-sm">
                  <template v-if="!row.check_out">
                    <RouterLink
                      v-if="canCheckout(row)"
                      :to="`/checkout/${row.id}`"
                      class="bg-green-500 hover:bg-green-700 text-white px-3 py-1 rounded"
                    >
                      Checkout
                    </RouterLink>

                    <span
                      v-else
                      class="bg-gray-300 text-gray-500 px-3 py-1 rounded"
                    >
                      Checkout
                    </span>
                  </template>

                  <span v-else class="text-gray-400"> - </span>
                </td>
              </tr>

              <tr v-if="!loading && filteredVisits.length === 0">
                <td colspan="8" class="text-center py-6 text-gray-500">
                  Tidak ada data visit
                </td>
              </tr>
            </tbody>
          </table>

          <!-- PAGINATION -->
          <div class="flex justify-between items-center px-4 py-3 bg-white border-t">
            <button
              @click="prevPage"
              :disabled="currentPage === 1"
              class="px-3 py-1 rounded bg-gray-200 disabled:opacity-50"
            >
              Prev
            </button>

            <span class="text-sm text-gray-600">
              {{ currentPage }} / {{ totalPages || 1 }}
            </span>

            <button
              @click="nextPage"
              :disabled="currentPage === totalPages"
              class="px-3 py-1 rounded bg-gray-200 disabled:opacity-50"
            >
              Next
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, computed, watch } from "vue";
import axios from "axios";
import { ArrowLeftFromLineIcon } from "lucide-vue-next";

const visits = ref([]);
const loading = ref(false);

const search = ref("");
const currentPage = ref(1);
const totalPages = ref(1);

const id_user = localStorage.id;
const branch = localStorage.branch;
const userRole = Number(localStorage.role);

const apiBase =
  (import.meta.env.VITE_API_BASE_URL ?? "http://localhost:8000") + "/api";

const token = localStorage.getItem("api_token") || "";

async function fetchVisits(page = 1) {
  loading.value = true;

  try {
    let url = "";

    if (userRole == 7) {
      url = `${apiBase}/optionvisitsalesreports/${id_user}?page=${page}`;
    } else if (userRole >= 4 && userRole <= 6) {
      url = `${apiBase}/optionvisitbranchsalesreports/${branch}?page=${page}`;
    } else {
      url = `${apiBase}/optionsalesreports?page=${page}`;
    }

    const res = await axios.get(url, {
      headers: { Authorization: `Bearer ${token}` },
    });

    visits.value = res.data.data;
    currentPage.value = res.data.current_page;
    totalPages.value = res.data.last_page;
  } catch (err) {
    console.error("Fetch visits error:", err);
  } finally {
    loading.value = false;
  }
}

const filteredVisits = computed(() => {
  if (!search.value) return visits.value;

  const q = search.value.toLowerCase();
  return visits.value.filter(
    (v) =>
      (v.customer_name || "").toLowerCase().includes(q) ||
      (v.project_name || "").toLowerCase().includes(q) ||
      (v.date || "").toLowerCase().includes(q) ||
      (v.user.name || "").toLowerCase().includes(q)
  );
});

function nextPage() {
  if (currentPage.value < totalPages.value) {
    fetchVisits(currentPage.value + 1);
  }
}

function prevPage() {
  if (currentPage.value > 1) {
    fetchVisits(currentPage.value - 1);
  }
}

watch(search, () => {
  currentPage.value = 1;
});

function formatDate(date) {
  return new Date(date).toLocaleDateString("id-ID", {
    day: "2-digit",
    month: "short",
    year: "numeric",
  });
}

function isExpired(row) {
  if (!row.date || row.check_out) return false;

  const visitDate = new Date(row.date);
  visitDate.setHours(23, 59, 59, 999);

  const now = new Date();

  return now > visitDate;
}

function canCheckout(row) {
  if (!isExpired(row)) return true;
  return userRole < 7;
}

onMounted(() => fetchVisits());
</script>