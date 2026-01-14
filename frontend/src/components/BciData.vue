<template>
    <div class="flex flex-col bg-[#10375C]/10 mx-2 my-2 rounded-xl">
        <!-- Header -->
        <div class="flex flex-row items-center px-4 py-4 text-3xl text-slate-600">
            <span>BCI</span>
            <strong class="ml-2">Database</strong>
        </div>
        <!-- Search Bar -->
         <div class="mx-3">
             <input v-model="searchQuery" type="text" placeholder="Cari berdasarkan Project ID, Stage, atau Town..."
             class=" bg-white w-full md:w-1/3 border border-gray-300 p-2 rounded-lg focus:ring-2 focus:ring-blue-400 focus:outline-none" />
         </div>
        <table class="mx-4 my-4 px-2 py-2  rounded-xl bg-slate-300 rounded-base border-default" >
            <thead class="">
                <tr class="bg-slate-400 text-md text-white">
                    <th class="py-2">Project Id</th>
                    <th>Project Name</th>
                    <th>Company Name</th>
                    <th>Project Stage</th>
                    <th>Project Town</th>
                    <th>PIC Name</th>
                    <th>Project Type</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody class="py-2 mx-2 ">
                <tr v-for="data in customersDatabase" class="my-2">
                    <td class="px-2 py-2 border-b border-slate-400">{{ data.project_id }}</td>
                    <td class="px-2 py-2 border-b border-slate-400">{{ data.project_name }}</td>
                    <td class="px-2 py-2 border-b border-slate-400">{{ data.company_name }}</td>
                    <td class="px-2 py-2 border-b border-slate-400">{{ data.project_stage }}</td>
                    <td class="px-2 py-2 border-b border-slate-400">{{ data.project_town }}</td>
                    <td class="px-2 py-2 border-b border-slate-400">{{ data.contact_first_name}}{{ data.contact_last_name}}</td>
                    <td class="px-2 py-2 border-b border-slate-400">{{ data.project_type}}</td>
                    <td class="px-2 py-2 border-b border-slate-400">
                        <select
                            v-model="data.status"
                            @change="handleAssign(data.id, data.status)"
                            :class="String(data.status).trim().toLowerCase() === 'open'
                            ? 'bg-green-400'
                            : 'bg-red-400'"
                        >
                            <option value="open">Open</option>
                            <option value="closed">Closed</option>
                        </select>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</template>

<script setup>
import { ref, computed, onMounted } from "vue";
import axios from "axios";

/* ===========================
   STATE
=========================== */
const customersDatabase = ref([]);
const searchQuery = ref("");
const loadingStatus = ref(null);

/* ===========================
   CONFIG
=========================== */
const apiBaseUrl = import.meta.env.VITE_API_BASE_URL;
const token  = localStorage.getItem("api_token")
const role   = Number(localStorage.getItem("role"))
const branch = localStorage.getItem("branch")

/* ===========================
   Computed: Search Filter
=========================== */
const filteredCustomers = computed(() => {
  // jika kolom pencarian kosong, tampilkan semua data
  if (!searchQuery.value) return customersDatabase.value;

  const q = searchQuery.value.toLowerCase();

  return customersDatabase.value.filter((item) => {
    // cek bidang utama proyek
    const matchesMainFields = [
      item.project_id,
      item.project_name,
      item.project_stage,
      item.project_town,
      item.project_start,
      item.project_end,
    ].some((field) => String(field || "").toLowerCase().includes(q));

    // cek nama perusahaan di dalam array "item"
    const matchesCompanies = Array.isArray(item.item)
      ? item.item.some((company) =>
          String(company.company_name || "").toLowerCase().includes(q)
        )
      : false;

    // hasil akhir: cocok di proyek atau di company
    return matchesMainFields || matchesCompanies;
  });
});

/* ===========================
   Fetch Data
=========================== */
const fetchCustomers = async () => {
  try {
    const token = localStorage.getItem("api_token");
    const res = await axios.get(`${apiBaseUrl}/api/allcustomerdatabase`, {
      headers: { Authorization: `Bearer ${token}` },
    });
    customersDatabase.value = res.data.data || [];
  } catch (err) {
    console.error("Gagal mengambil data:", err);
  }
};

/* ================= ASSIGNMENT LOGIC ================= */
const handleAssign = async (projectId, status) => {
  loadingStatus.value = projectId;
  try {
    await axios.put(`${apiBaseUrl}/api/updatestatuscustomerdatabase`, {
      project_id: projectId,
      status: status
    }, {
      headers: { Authorization: `Bearer ${token}` }
    });
    
    // Update local state secara manual agar tidak perlu refresh API full
    const index = customersDatabase.value.findIndex(c => c.id === projectId);
    if (index !== -1) {
      customersDatabase.value[index].status = status;
    }
  } catch (err) {
    console.error(err);
    alert("Gagal memperbarui status. Periksa koneksi.");
  } finally {
    loadingStatus.value = null;
  }
};

/* ===========================
   Init
=========================== */
onMounted(() => {
  fetchCustomers();
  
});

</script>

<style lang="scss" scoped></style>