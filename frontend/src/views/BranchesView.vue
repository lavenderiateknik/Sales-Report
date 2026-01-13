<template>
  <div class="bg-[#10375C]/10 rounded-2xl mx-4 my-2 py-4 p-4">
    <!-- Header + Button -->
    <div class="flex justify-between items-center mb-4">
      <button 
        @click="showModal = true" 
        class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg"
      >
        + Add Branch
      </button>
    </div>

    <!-- Table Wrapper -->
    <div class="border border-amber-500 m-3 p-3 rounded-xl bg-white shadow-xl overflow-x-auto">
      
      <!-- TITLE + SEARCH -->
      <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-5 gap-3">
        <h2 class="text-3xl">Branch <strong>List</strong></h2>

        <!-- SEARCH INPUT -->
        <input
          v-model="search"
          type="text"
          placeholder="Search branch..."
          class="border rounded-lg px-3 py-2 w-full md:w-64 focus:outline-none focus:ring-2 focus:ring-blue-400"
        />
      </div>

      <div v-if="loading" class="text-center py-8 text-lg text-gray-500">
        Loading data...
      </div>

      <table v-else class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
          <tr>
            <th v-for="col in colsDataBranch" :key="col.field" scope="col"
                class="px-6 py-3 text-xs font-medium text-gray-500 uppercase tracking-wider"
                :class="{'text-center': col.align === 'center'}"
            >
              {{ col.title }}
            </th>
          </tr>
        </thead>

        <tbody class="bg-white divide-y divide-gray-200">
          <tr v-for="branch in filteredBranches" :key="branch.id">
            <td class="px-6 py-4 text-sm text-gray-500 text-center">
              {{ branch.no }}
            </td>
            <td class="px-6 py-4 text-sm font-medium text-gray-900 text-center">
              {{ branch.name }}
            </td>

            <td class="px-6 py-4 text-sm font-medium text-center">
              <a 
                href="#" 
                class="text-blue-600 hover:text-blue-800 font-medium mr-4"
                @click.prevent="editBranch(branch.id)" 
              >
                Edit
              </a>
              <button 
                class="text-red-600 hover:text-red-800 font-medium" 
                @click="deleteBranch(branch.id)"
              >
                Delete
              </button>
            </td>
          </tr>

          <tr v-if="!loading && filteredBranches.length === 0">
            <td :colspan="colsDataBranch.length" class="px-6 py-4 text-center text-gray-500">
              Data tidak ditemukan.
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Modal Add -->
    <div v-if="showModal" class="fixed inset-0 bg-black/50 flex items-center justify-center z-50">
      <div class="bg-slate-100 m-4 px-4 py-3 rounded-2xl shadow-xl w-full max-w-lg relative">
        <button 
          @click="showModal = false" 
          class="absolute top-2 right-2 text-gray-500 hover:text-gray-700 text-xl"
        >
          ✕
        </button>
        <h3 class="text-xl font-bold">Add Branch</h3>
        <AddBranchForm @saved="handleBranchSaved" />
      </div>
    </div>

    <!-- Modal Edit -->
    <div v-if="showEditModal" class="fixed inset-0 bg-black/50 flex items-center justify-center z-50">
      <div class="bg-white m-4 p-6 rounded-2xl shadow-lg w-full max-w-lg relative">
        <button 
          @click="showEditModal = false" 
          class="absolute top-2 right-2 text-gray-500 hover:text-gray-700 text-xl"
        >
          ✕
        </button>
        <h3 class="text-xl font-bold mb-4">Edit Branch ID: {{ editingBranchId }}</h3>
        
        <EditBranchForm 
          :branch-id="editingBranchId" 
          @saved="handleEditSaved"
        />
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from "vue";
import axios from "axios";
import AddBranchForm from "@/components/AddBranchForm.vue";
import EditBranchForm from "@/components/EditBranchForm.vue";

const apiBaseUrl = import.meta.env.VITE_API_BASE_URL;
const token = localStorage.getItem("api_token");

const allBranches = ref([]);   // 🔹 data asli
const search = ref("");

const showModal = ref(false);
const showEditModal = ref(false);
const editingBranchId = ref(null);
const loading = ref(false);

// --- Fetch Branch Data ---
const fetchBranches = async () => {
  loading.value = true;
  try {
    const res = await axios.get(`${apiBaseUrl}/api/allbranches`, {
      headers: { Authorization: `Bearer ${token}` },
    });

    allBranches.value = (res.data.data || []).map((item, index) => ({
      ...item,
      no: index + 1,
    }));
  } catch (err) {
    console.error("Error fetching branches:", err);
  } finally {
    loading.value = false;
  }
};

// --- FILTER SEARCH ---
const filteredBranches = computed(() => {
  if (!search.value) return allBranches.value;

  return allBranches.value.filter(b =>
    b.name.toLowerCase().includes(search.value.toLowerCase())
  );
});

// --- Columns ---
const colsDataBranch = ref([
  { field: "no", title: "No", align: "center" },
  { field: "name", title: "Branch Name", align: "center" },
  { field: "actions", title: "Action", align: "center" }
]);

// --- Actions ---
const editBranch = (id) => {
  editingBranchId.value = id;
  showEditModal.value = true;
};

const deleteBranch = async (id) => {
  try {
    await axios.delete(`${apiBaseUrl}/api/branches/${id}`, {
      headers: { Authorization: `Bearer ${token}` }
    });
    alert("Branch berhasil dihapus");
    fetchBranches();
  } catch (err) {
    console.error("Gagal menghapus branch:", err);
    alert("Gagal menghapus branch");
  }
};

const handleBranchSaved = () => {
  fetchBranches();
  showModal.value = false;
};

const handleEditSaved = () => {
  fetchBranches();
  showEditModal.value = false;
};

onMounted(fetchBranches);
</script>
