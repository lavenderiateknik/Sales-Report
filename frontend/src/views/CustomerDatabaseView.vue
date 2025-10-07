<template>
  <div class="flex flex-col bg-[#10375C]/10 mx-2 my-2 rounded-2xl">
    <!-- Header -->
    <div class="flex flex-row items-center px-4 py-4 text-3xl text-slate-600">
      <span>Customer</span>
      <strong class="ml-2">Database</strong>
    </div>

    <!-- Tombol Upload -->
    <div class="flex justify-between items-center mb-4 px-4">
      <button
        @click="showModal = true"
        class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg"
      >
        + Import Database
      </button>
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

        <!-- Pilih Branch -->
        <label class="block mb-2 text-gray-600">Pilih Branch</label>
        <select v-model="selectedBranch" class="border border-gray-300 p-2 rounded-md w-full mb-4">
          <option value="" disabled>Pilih Branch</option>
          <option v-for="branch in branches" :key="branch.id" :value="branch.id">
            {{ branch.name }}
          </option>
        </select>

        <!-- Upload File -->
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
      <Tabel
        :rows-data="customersDatabase"
        :cols="colsCustomerDatabase"
        title1="BCI"
        title2="Database"
      />
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from "vue";
import axios from "axios";
import Tabel from "@/components/Tabel.vue";
import currency from 'currency.js';

/* ===========================
   Helpers
=========================== */
function formatCurrency(value, options = { symbol: 'Rp ', separator: '.', decimal: ',', precision: 0 }) {
  if (value === null || value === undefined || value === '') return '-';
  const num = Number(value);
  if (!Number.isFinite(num)) return '-';
  return currency(num, options).format();
}

function formatDate(value) {
  if (!value) return '-';
  const d = new Date(value);
  if (Number.isNaN(d.getTime())) return String(value);
  return d.toLocaleDateString('id-ID', {
    day: '2-digit',
    month: 'long',
    year: 'numeric',
  });
}

const apiBaseUrl = import.meta.env.VITE_API_BASE_URL;

// State
const showModal = ref(false);
const selectedFile = ref(null);
const selectedBranch = ref("");
const loading = ref(false);
const customersDatabase = ref([]);
const branches = ref([]);


// Definisi kolom lengkap sesuai data Excel
const colsCustomerDatabase = ref([
  { field: "project_id", title: "Project ID" },
  { field: "project_type", title: "Project Type" },
  { field: "development_type", title: "Development Type" },
  { field: "project_name", title: "Project Name", minWidth: '150px', },
  { field: "project_detail", title: "Project Detail",minWidth: '500px', },
  { field: "project_stage", title: "Project Stage" },
  { field: "project_region", title: "Project Region" },
  { field: "project_street_name", title: "Project Street Name", minWidth: '300px' },
  { field: "local_value", title: "Local Value", minWidth: '300px' },
  { field: "construction_end_date", title: "Construction End Date" },
  { field: "construction_start_date", title: "Construction Start Date" },
  { field: "floor_area", title: "Floor Area (m²)" },
  { field: "site_area", title: "Site Area (m²)" },
  { field: "storeys", title: "Storeys" },
  { field: "role_on_project", title: "Role on Project" },
  { field: "last_update", title: "Last Update" },
  { field: "project_address", title: "Project Address", minWidth: '300px', },
  { field: "project_town_suburb", title: "Project Town / Suburb" },
  { field: "company_website", title: "Company Website" },
  { field: "project_province_state", title: "Project Province / State" },
  { field: "post_code", title: "Post Code" },
  { field: "company_name", title: "Company Name" },
  { field: "company_street_name", title: "Company Street Name", minWidth: '300px' },
  { field: "company_roles", title: "Company Roles" },
  { field: "company_town_suburb", title: "Company Town / Suburb" },
  { field: "company_state_province", title: "Company State / Province" },
  { field: "company_phone", title: "Company Phone" },
  { field: "company_email", title: "Company Email" },
  { field: "contact_first_name", title: "Contact First Name" },
  { field: "contact_surname", title: "Contact Surname" },
  { field: "contact_position", title: "Contact Position" },
  { field: "contact_landline", title: "Contact Landline" },
  { field: "contact_email", title: "Contact Email" },
  { field: "contact_remark", title: "Contact Remark" },
  { field: "company_region", title: "Company Region" },
  { field: "mobile", title: "Mobile" },
  { field: "role_status", title: "Role Status" },
  { field: "construction_start_text", title: "Construction Start Date" },
  { field: "construction_end_text", title: "Construction End Date" },
]);

// Ambil data customer saat halaman dibuka
const fetchCustomers = async () => {
  try {
    const token = localStorage.getItem("api_token");
    const res = await axios.get(`${apiBaseUrl}/api/customerdatabase`, {
      headers: { Authorization: `Bearer ${token}` },
    });

    // 🔹 Format data sebelum disimpan ke state
    customersDatabase.value = (res.data.data || []).map((item) => ({
      ...item,
      local_value: formatCurrency(item.local_value), // format ke Rp
      last_update: formatDate(item.last_update),
    }));
  } catch (err) {
    console.error("Gagal mengambil data:", err);
  }
};

// Ambil data branch saat halaman dibuka
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

// Upload Excel ke backend
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
        Authorization: `Bearer ${token}`
      },
    });
    alert("Import berhasil!");
    showModal.value = false;
    selectedFile.value = null;
    selectedBranch.value = "";
    await fetchCustomers(); // Refresh data
  } catch (error) {
    console.error("Gagal upload:", error);
    alert("Terjadi kesalahan saat mengupload file.");
  } finally {
    loading.value = false;
  }
};

onMounted(() => {
  fetchCustomers();
  fetchBranches();
});
</script>

<style scoped>
/* Agar tabel bisa discroll secara horizontal */
.table-container {
  overflow-x: auto;
}
</style>
