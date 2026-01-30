<template>
  <div class="flex flex-col bg-[#10375C]/10 mx-2 my-2 rounded-xl">
    <div v-if="isAllowed">
      <!-- Header -->
      <div class="flex flex-row items-center px-4 py-4 text-3xl text-slate-600">
        <span>BCI</span>
        <strong class="ml-2">Database</strong>
      </div>
      <!-- Search -->
      <div class="mx-3">
        <input
          v-model="searchQuery"
          type="text"
          placeholder="Cari berdasarkan Project ID, Stage, atau Town..."
          class="bg-white w-full md:w-1/3 border border-gray-300 p-2 rounded-lg
                 focus:ring-2 focus:ring-blue-400 focus:outline-none"
        />
      </div>
      <!-- Table -->
      <div class="mx-4 my-4 overflow-x-auto rounded-xl bg-slate-300">
        <table class="min-w-full text-sm">
          <thead>
            <tr class="bg-slate-500 text-white text-xs md:text-sm">
              <th class="py-2 px-3 text-left">Project ID</th>
              <th class="py-2 px-3 text-left">Project Name</th>
              <th class="py-2 px-3">Company</th>
              <th class="hidden lg:table-cell py-2 px-3">Stage</th>
              <th class="hidden lg:table-cell py-2 px-3">Town</th>
              <th class="hidden xl:table-cell py-2 px-3">PIC</th>
              <th class="hidden xl:table-cell py-2 px-3">Type</th>
              <th class="py-2 px-3 text-center">Status</th>
            </tr>
          </thead>
  
          <tbody>
            <tr
              v-for="data in filteredCustomers"
              :key="data.id"
              class="bg-slate-200 odd:bg-slate-100 hover:bg-slate-300 transition"
            >
              <td class="px-3 py-2 font-semibold">
                {{ data.project_id }}
              </td>
  
              <td class="px-3 py-2">
                {{ data.project_name }}
              </td>
  
              <td class="px-3 py-2">
                {{ data.company_name }}
              </td>
  
              <td class="hidden lg:table-cell px-3 py-2">
                {{ data.project_stage }}
              </td>
  
              <td class="hidden lg:table-cell px-3 py-2">
                {{ data.project_town }}
              </td>
  
              <td class="hidden xl:table-cell px-3 py-2">
                {{ data.contact_first_name }} {{ data.contact_surname }}
              </td>
  
              <td class="hidden xl:table-cell px-3 py-2">
                {{ data.project_type }}
              </td>
  
              <!-- STATUS (PROJECT-BASED) -->
              <td class="px-3 py-2 text-center">
                <select
                  v-model="data.status"
                  @change="handleAssign(data.project_id, data.status)"
                  :disabled="loadingStatus === data.project_id"
                  class="px-2 py-1 rounded-md text-white text-xs md:text-sm
                         focus:outline-none disabled:opacity-50"
                  :class="String(data.status).trim().toLowerCase() === 'open'
                    ? 'bg-green-500'
                    : 'bg-red-500'"
                >
                  <option value="open">Open</option>
                  <option value="closed">Closed</option>
                </select>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
    <div v-else class="p-6 text-center text-red-600">
        🚫 Anda tidak memiliki akses ke halaman ini
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from "vue";
import axios from "axios";

/* =========================
   STATE
========================= */
const customersDatabase = ref([]);
const searchQuery = ref("");
const loadingStatus = ref(null);
const role = Number(localStorage.getItem("role"));
const isAllowed = computed(() => role <= 6);

/* =========================
   CONFIG
========================= */
const apiBaseUrl = import.meta.env.VITE_API_BASE_URL;
const token = localStorage.getItem("api_token");

/* =========================
   SEARCH FILTER
========================= */
const filteredCustomers = computed(() => {
  if (!searchQuery.value) return customersDatabase.value;

  const q = searchQuery.value.toLowerCase();

  return customersDatabase.value.filter(item =>
    [
      item.project_id,
      item.project_name,
      item.company_name,
      item.project_stage,
      item.project_town
    ].some(field =>
      String(field || "").toLowerCase().includes(q)
    )
  );
});

/* =========================
   FETCH DATA
========================= */
const fetchCustomers = async () => {
  try {
    const res = await axios.get(
      `${apiBaseUrl}/api/allcustomerdatabase`,
      {
        headers: { Authorization: `Bearer ${token}` }
      }
    );

    customersDatabase.value = res.data.data || [];
  } catch (err) {
    console.error("Gagal mengambil data:", err);
  }
};

/* =========================
   UPDATE STATUS (PROJECT BASED)
========================= */
const handleAssign = async (projectId, status) => {
  loadingStatus.value = projectId;

  try {
    await axios.put(
      `${apiBaseUrl}/api/updatestatuscustomerdatabase`,
      {
        project_id: projectId,
        status: status
      },
      {
        headers: { Authorization: `Bearer ${token}` }
      }
    );

    // Sinkron semua row dengan project_id sama
    customersDatabase.value = customersDatabase.value.map(item =>
      item.project_id === projectId
        ? { ...item, status }
        : item
    );

  } catch (err) {
    console.error(err);
    alert("Gagal memperbarui status.");
  } finally {
    loadingStatus.value = null;
  }
};

/* =========================
   INIT
========================= */
onMounted(fetchCustomers);
</script>

<style scoped></style>
