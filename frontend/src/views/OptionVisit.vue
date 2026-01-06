<template>
  <div class="flex flex-col bg-[#10375C]/10 mx-2 my-2 px-2 py-2 rounded-md gap-1">

    <RouterLink
      to="/optionreport" class="flex gap-2 place-items-center">
      <ArrowLeftFromLineIcon class="w-4"/> Back
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
        <div class="inline-block min-w-full shadow rounded-lg overflow-hidden">

          <table class="min-w-full leading-normal">
            <thead>
              <tr>
                <th class="px-5 py-3 border-b-2 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase">
                  Nama Perusahaan
                </th>
                <th class="px-5 py-3 border-b-2 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase">
                  Nama Proyek
                </th>
                <th class="px-5 py-3 border-b-2 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase">
                  Tanggal
                </th>
                <th class="px-5 py-3 border-b-2 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase">
                  Checkin
                </th>
                <th class="px-5 py-3 border-b-2 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase">
                  Checkout
                </th>
                <th class="px-5 py-3 border-b-2 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase">
                  Status
                </th>
                <th class="px-5 py-3 border-b-2 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase">
                  Aksi
                </th>
              </tr>
            </thead>

            <tbody>
              <tr
                v-for="row in visits"
                :key="row.id"
              >
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
                  <span
                    class="relative inline-block px-3 py-1 font-semibold text-white leading-tight"
                  >
                    <span
                      aria-hidden
                      class="absolute inset-0 rounded-full opacity-50"
                      :class="row.check_out ? 'bg-green-600' : 'bg-red-600'"
                    ></span>
                    <span class="relative">
                      {{ row.check_out ? "Sudah Checkout" : "Belum Checkout" }}
                    </span>
                  </span>
                </td>

                <td class="px-5 py-5 border-b bg-white text-sm">
                  <RouterLink
                    v-if="!row.check_out"
                    :to="`/checkout/${row.id}`"
                    class="bg-green-500 hover:bg-green-700 text-white px-3 py-1 rounded"
                  >
                    Checkout
                  </RouterLink>

                  <span v-else class="text-gray-400">
                    -
                  </span>
                </td>
              </tr>

              <tr v-if="!loading && visits.length === 0">
                <td colspan="7" class="text-center py-6 text-gray-500">
                  Tidak ada data visit
                </td>
              </tr>
            </tbody>
          </table>

        </div>
      </div>
    </div>
  </div>
</template>


<script setup>
import { ref, onMounted } from "vue";
import axios from "axios";
import { ArrowLeftFromLineIcon } from "lucide-vue-next";


/* =====================
   STATE
===================== */
const visits = ref([]);
const loading = ref(false);
const id_user = localStorage.id;


/* =====================
   CONFIG
===================== */
const apiBase =
  (import.meta.env.VITE_API_BASE_URL ?? "http://localhost:8000") + "/api";

const token = localStorage.getItem("api_token") ||"";

/* =====================
   FETCH VISITS
===================== */
async function fetchVisits() {
  loading.value = true;
  try {
    const res = await axios.get(`${apiBase}/salesreports/${id_user}`, {
      headers: {
        Authorization: `Bearer ${token}`,
      },
    });

    visits.value = res.data.data ?? [];
  } finally {
    loading.value = false;
  }
}

/* =====================
   HELPER
===================== */
function formatDate(date) {
  return new Date(date).toLocaleDateString("id-ID", {
    day: "2-digit",
    month: "short",
    year: "numeric",
  });
}

/* =====================
   MOUNT
===================== */
onMounted(fetchVisits);
</script>


<style lang="scss" scoped></style>