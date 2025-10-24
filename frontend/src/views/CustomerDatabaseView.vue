<template>
  <div class="flex flex-col bg-[#10375C]/10 mx-2 my-2 rounded-2xl">
    <!-- Header -->
    <div class="flex flex-row items-center px-4 py-4 text-3xl text-slate-600">
      <span>Customer</span>
      <strong class="ml-2">Database</strong>
    </div>

    <!-- Tombol Upload & Search -->
    <div class="flex flex-col md:flex-row justify-between items-center mb-4 px-4 gap-3">
      <button
        @click="showModal = true"
        class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg"
      >
        + Import Database
      </button>

      <!-- Search Bar -->
      <input
        v-model="searchQuery"
        type="text"
        placeholder="Cari berdasarkan Project ID, Stage, atau Town..."
        class="w-full md:w-1/3 border border-gray-300 p-2 rounded-lg focus:ring-2 focus:ring-blue-400 focus:outline-none"
      />
    </div>

    <!-- Modal Upload -->
    <div
      v-if="showModal"
      class="fixed inset-0 flex items-center justify-center bg-black/50 z-50"
    >
      <div class="bg-white p-6 rounded-xl w-[420px] shadow-lg">
        <h2 class="text-lg font-semibold mb-4 text-gray-700">
          Import Excel Customer
        </h2>

        <label class="block mb-2 text-gray-600">Pilih Branch</label>
        <select
          v-model="selectedBranch"
          class="border border-gray-300 p-2 rounded-md w-full mb-4"
        >
          <option value="" disabled>Pilih Branch</option>
          <option
            v-for="branch in branches"
            :key="branch.id"
            :value="branch.id"
          >
            {{ branch.name }}
          </option>
        </select>

        <input
          type="file"
          accept=".xlsx, .xls"
          @change="handleFileChange"
          class="border border-gray-300 p-2 rounded-md w-full mb-4"
        />

        <div class="flex justify-end gap-2 mt-4">
          <button
            @click="showModal = false"
            class="px-3 py-2 rounded-md bg-gray-200 hover:bg-gray-300"
          >
            Batal
          </button>
          <button
            @click="uploadExcel"
            :disabled="loading || !selectedFile || !selectedBranch"
            class="px-3 py-2 rounded-md bg-blue-600 hover:bg-blue-700 text-white disabled:opacity-50"
          >
            {{ loading ? "Mengunggah..." : "Upload" }}
          </button>
        </div>
      </div>
    </div>

    <!-- Tabel -->
    <div class="overflow-x-auto px-4 pb-4">
      <table
        class="min-w-full bg-white border-2 border-[#EB8317] rounded-2xl shadow-xl overflow-hidden"
      >
        <thead class="bg-[#F3C623] text-slate-600 text-sm">
          <tr>
            <th class="py-3 px-4 text-left font-semibold border-b">Project ID</th>
            <th class="py-3 px-4 text-left font-semibold border-b">Project Name</th>
            <th class="py-3 px-4 text-left font-semibold border-b hidden lg:table-cell">Project Stage</th>
            <th class="py-3 px-4 text-left font-semibold border-b hidden lg:table-cell">Project Town</th>
            <th class="py-3 px-4 text-center font-semibold border-b">Detail</th>
          </tr>
        </thead>
        <tbody>
          <tr
            v-for="(item, index) in paginatedCustomers"
            :key="item.project_id"
            class="border-b hover:bg-blue-300"
          >
            <td class="py-3 px-4 text-gray-700">{{ item.project_id }}</td>
            <td class="py-3 px-4 text-gray-700">{{ item.project_name || "-" }}</td>
            <td class="py-3 px-4 text-gray-700 hidden lg:table-cell">{{ item.project_stage || "-" }}</td>
            <td class="py-3 px-4 text-gray-700 hidden lg:table-cell">{{ item.project_town || "-" }}</td>
            <td class="py-3 px-4 text-center">
              <button
                @click="openProjectDetail(item.project_id)"
                class="text-sm bg-blue-600 hover:bg-blue-700 text-white px-3 py-1.5 rounded-md"
              >
                Open
              </button>
            </td>
          </tr>
        </tbody>
      </table>

      <!-- Pagination -->
      <div class="flex justify-between items-center mt-4 text-sm text-gray-600">
        <div>Menampilkan {{ startItem }}–{{ endItem }} dari {{ filteredCustomers.length }} data</div>
        <div class="flex items-center gap-2">
          <button
            @click="prevPage"
            :disabled="currentPage === 1"
            class="px-3 py-1 rounded-md bg-gray-200 hover:bg-gray-300 disabled:opacity-50"
          >
            Prev
          </button>
          <span>Halaman {{ currentPage }} / {{ totalPages }}</span>
          <button
            @click="nextPage"
            :disabled="currentPage === totalPages"
            class="px-3 py-1 rounded-md bg-gray-200 hover:bg-gray-300 disabled:opacity-50"
          >
            Next
          </button>
        </div>
      </div>
    </div>
  </div>

  <!-- Modal Detail Project -->
  <!-- (isi modal project tetap seperti versi kamu — tidak diubah sama sekali) -->
</template>

<script setup>
import { ref, computed, onMounted } from "vue";
import axios from "axios";
import currency from "currency.js";

const apiBaseUrl = import.meta.env.VITE_API_BASE_URL;

const showModal = ref(false);
const selectedFile = ref(null);
const selectedBranch = ref("");
const loading = ref(false);
const customersDatabase = ref([]);
const branches = ref([]);
const searchQuery = ref("");

// Pagination
const currentPage = ref(1);
const itemsPerPage = ref(10); // ubah sesuai kebutuhan

const selectedProject = ref([]);
const projectDetail = ref(null);
const showDetailModal = ref(false);

/* ===========================
   Computed: Search Filter
=========================== */
const filteredCustomers = computed(() => {
  if (!searchQuery.value) return customersDatabase.value;
  const q = searchQuery.value.toLowerCase();
  return customersDatabase.value.filter((item) =>
    [item.project_id, item.project_stage, item.project_town]
      .some((field) => String(field || "").toLowerCase().includes(q))
  );
});

/* ===========================
   Pagination Computed
=========================== */
const totalPages = computed(() => Math.ceil(filteredCustomers.value.length / itemsPerPage.value));

const paginatedCustomers = computed(() => {
  const start = (currentPage.value - 1) * itemsPerPage.value;
  return filteredCustomers.value.slice(start, start + itemsPerPage.value);
});

const startItem = computed(() => (filteredCustomers.value.length ? (currentPage.value - 1) * itemsPerPage.value + 1 : 0));
const endItem = computed(() => Math.min(currentPage.value * itemsPerPage.value, filteredCustomers.value.length));

const nextPage = () => {
  if (currentPage.value < totalPages.value) currentPage.value++;
};
const prevPage = () => {
  if (currentPage.value > 1) currentPage.value--;
};

/* ===========================
   Helpers
=========================== */
function formatCurrency(value, options = { symbol: "Rp ", separator: ".", decimal: ",", precision: 0 }) {
  if (!value) return "-";
  const num = Number(value);
  if (!Number.isFinite(num)) return "-";
  return currency(num, options).format();
}

/* ===========================
   Fetch Data
=========================== */
const fetchCustomers = async () => {
  try {
    const token = localStorage.getItem("api_token");
    const res = await axios.get(`${apiBaseUrl}/api/customerdatabase`, {
      headers: { Authorization: `Bearer ${token}` },
    });
    customersDatabase.value = res.data.data || [];
  } catch (err) {
    console.error("Gagal mengambil data:", err);
  }
};

const fetchBranches = async () => {
  try {
    const token = localStorage.getItem("api_token");
    const res = await axios.get(`${apiBaseUrl}/api/allbranches`, {
      headers: { Authorization: `Bearer ${token}` },
    });
    branches.value = res.data.data;
  } catch (err) {
    console.error("Gagal mengambil branch:", err);
  }
};

/* ===========================
   Upload Excel
=========================== */
const handleFileChange = (e) => {
  selectedFile.value = e.target.files[0];
};

const uploadExcel = async () => {
  if (!selectedFile.value || !selectedBranch.value) return;

  const token = localStorage.getItem("api_token");
  const formData = new FormData();
  formData.append("file", selectedFile.value);
  formData.append("id_branch", selectedBranch.value);

  try {
    loading.value = true;
    await axios.post(`${apiBaseUrl}/api/import-customer-database`, formData, {
      headers: {
        "Content-Type": "multipart/form-data",
        Authorization: `Bearer ${token}`,
      },
    });
    alert("Import berhasil!");
    showModal.value = false;
    selectedFile.value = null;
    selectedBranch.value = "";
    await fetchCustomers();
  } catch (error) {
    console.error("Gagal upload:", error);
    alert("Terjadi kesalahan saat mengupload file.");
  } finally {
    loading.value = false;
  }
};

/* ===========================
   Modal Detail Project
=========================== */
const openProjectDetail = async (projectId) => {
  const token = localStorage.getItem("api_token");
  try {
    const res = await axios.get(`${apiBaseUrl}/api/customer-database/project/${projectId}`, {
      headers: { Authorization: `Bearer ${token}` },
    });
    projectDetail.value = res.data.project;
    selectedProject.value = res.data.companies;
    showDetailModal.value = true;
  } catch (err) {
    console.error("Gagal mengambil detail project:", err);
  }
};

/* ===========================
   Init
=========================== */
onMounted(() => {
  fetchCustomers();
  fetchBranches();
});
</script>
