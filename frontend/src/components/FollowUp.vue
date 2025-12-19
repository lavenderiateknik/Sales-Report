<template>
  <div class="bg-gray-100 mx-auto max-w-lg p-6 rounded-2xl border border-gray-300 shadow-xl/50 shadow-gray-400">
    <form @submit.prevent="submitForm" enctype="multipart/form-data" class="space-y-4">
      <h1 class="text-3xl font-bold text-center text-orange-600 mb-1">
        Report <strong class="text-orange-900">Sales</strong> 📊
      </h1>

      <div>
        <label for="type_report_id" class="block text-sm font-medium text-gray-700 mb-1">Tipe Laporan</label>
        <select id="type_report_id" v-model="form.type_report_id" :disabled="loading.typeReports"
          class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-orange-500">
          <option value="" disabled>Pilih Tipe Laporan</option>
          <option v-for="tr in typeReports.filter(t => t.id !== 6 && t.id !== 1)" :key="tr.id" :value="tr.id">{{ tr.name }}</option>
        </select>
      </div>

      <div>
        <label for="date" class="block text-sm font-medium text-gray-700 mb-1">Date</label>
        <input id="date" type="date" v-model="form.date" required
          class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-orange-500" />
      </div>

      <div>
        <label for="type_customer_id" class="block text-sm font-medium text-gray-700 mb-1">Type Customer</label>
        <select id="type_customer_id" v-model="form.type_customer_id" :disabled="loading.typeCustomers"
          class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-orange-500">
          <option value="" disabled>Pilih Tipe Pelanggan</option>
          <option v-for="tc in typeCustomers" :key="tc.id" :value="tc.id">{{ tc.name }}</option>
        </select>
      </div>

      <div class="relative">
        <label class="block text-sm font-medium text-gray-700 mb-1">Pilih Perusahaan / Proyek</label>
        <div @click="isOpen = !isOpen"
          class="w-full p-2 border rounded-md bg-white cursor-pointer shadow-sm flex justify-between items-center focus:ring-2 focus:ring-orange-500 min-h-[42px]">
          <div class="flex-1">
            <template v-if="selectedCustomer">
              <div class="text-sm font-bold text-gray-800">{{ selectedCustomer.customer_name }}</div>
              <div class="text-xs text-gray-500 truncate">{{ selectedCustomer.project_name }}</div>
            </template>
            <span v-else class="text-sm text-gray-400">Cari Proyek...</span>
          </div>
          <svg class="w-4 h-4 text-gray-400 transition-transform" :class="{'rotate-180': isOpen}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
          </svg>
        </div>

        <div v-if="isOpen" class="absolute z-50 mt-1 w-full max-h-60 overflow-auto bg-white border rounded-md shadow-xl">
          <div v-for="customer in customers" :key="customer.id" @click="selectItem(customer)"
            class="p-3 text-sm hover:bg-orange-50 cursor-pointer border-b last:border-0">
            <div class="font-bold text-gray-800">{{ customer.customer_name }}</div>
            <div class="text-xs text-gray-500">{{ customer.project_name }} || {{ customer.type_project }}</div>
          </div>
        </div>
      </div>

      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Tipe Proyek</label>
        <input type="text" v-model="form.type_project_id" readonly
          class="w-full px-3 py-2 border border-gray-300 rounded-md bg-gray-50 text-gray-600 cursor-not-allowed">
      </div>

      <div>
        <label for="project_name" class="block text-sm font-medium text-gray-700 mb-1">Nama Proyek</label>
        <input id="project_name" type="text" v-model="form.project_name" readonly
          class="w-full px-3 py-2 border border-gray-300 rounded-md bg-gray-50 text-gray-600 cursor-not-allowed" />
      </div>

      <div class="grid md:grid-cols-2 md:gap-6">
        <div>
          <label for="pic_name" class="block text-sm font-medium text-gray-700 mb-1">Nama PIC</label>
          <input id="pic_name" type="text" v-model="form.pic_name" required
            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-orange-500" />
        </div>
        <div>
          <label for="pic_phone" class="block text-sm font-medium text-gray-700 mb-1">Nomor Telepon PIC</label>
          <input id="pic_phone" type="tel" v-model="form.pic_phone" required
            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-orange-500" />
        </div>
      </div>

      <div class="grid md:grid-cols-2 md:gap-6">
        <div>
          <label for="pic_name" class="block text-sm font-medium text-gray-700 mb-1">Catatan Laporan</label>
          <input id="pic_name" type="text" v-model="form.report_notes" required
            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-orange-500" />
        </div>
        <div>
          <label for="pic_phone" class="block text-sm font-medium text-gray-700 mb-1">Kebutuhan alat</label>
          <textarea col id="equipment_needs" type="tel" v-model="form.equipment_needs" required
            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-orange-500" />
        </div>
      </div>
      <!-- PURCHASE ORDER (HANYA JIKA TYPE REPORT = 5) -->
      <div v-if="form.type_report_id === 5">
        <label class="block text-sm font-medium text-gray-700 mb-1">
          Nominal Purchase Order
        </label>
        <input
          type="number"
          v-model="form.nominal_purchase_order"
          placeholder="Masukkan nominal PO"
          class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-orange-500"
        />
      </div>

      <div class="mb-4">
        <label class="block text-sm font-medium text-gray-700 mb-1">Unggah Foto</label>
        <input type="file" class="w-full" accept="image/*" @change="onFileChange" />
        <div v-if="previewUrl" class="mt-2">
          <img :src="previewUrl" class="h-40 w-full object-cover rounded-md border" />
          <button type="button" class="text-red-500 text-sm mt-1 underline" @click="clearPreview">Hapus Foto</button>
        </div>
      </div>
      

      <div class="text-center pt-4">
        <button type="submit" :disabled="submitting"
          class="w-full bg-orange-600 hover:bg-orange-700 text-white font-bold py-3 px-4 rounded-md transition duration-200">
          {{ submitting ? 'Menyimpan...' : 'Kirim Laporan' }}
        </button>
      </div>
    </form>
  </div>
</template>

<script setup>
import { ref, onMounted, watch  } from "vue";
import axios from "axios";


const previewUrl = ref(null);



function onFileChange(e) {
  const file = e.target.files[0];
  if (!file) return;

  // Validasi image
  if (!file.type.startsWith("image/")) {
    alert("File harus berupa gambar");
    return;
  }

  form.value.picture = file;

  // Hapus preview lama (hindari memory leak)
  if (previewUrl.value) {
    URL.revokeObjectURL(previewUrl.value);
  }

  previewUrl.value = URL.createObjectURL(file);
}

function clearPreview() {
  if (previewUrl.value) {
    URL.revokeObjectURL(previewUrl.value);
  }
  previewUrl.value = null;
  form.value.picture = null;
}

// --- KONSTANTA & CONFIG ---
const apiBase = (import.meta.env.VITE_API_BASE_URL ?? "http://localhost:8000") + "/api";

// Pastikan "Bearer " ditambahkan jika tidak ada dalam localStorage
const rawToken = localStorage.getItem("api_token") || localStorage.getItem("token") || "";
const token = rawToken.startsWith("Bearer ") ? rawToken : `Bearer ${rawToken}`;

const id_user = localStorage.getItem("id") || "";

// --- STATE ---
const isOpen = ref(false);
const selectedCustomer = ref(null);
const submitting = ref(false);
const typeCustomers = ref([]);
const typeReports = ref([]);
const customers = ref([]);
const loading = ref({ typeCustomers: false, typeReports: false });

const form = ref({
  date: new Date().toISOString().split("T")[0],
  type_customer_id: "",
  type_report_id: "",
  customer_name: "",
  project_name: "",
  type_project_id: "", 
  pic_name: "",
  pic_phone: "",
  pic_position: "",
  report_notes: "",
  nominal_purchase_order: null,
  picture: null,
});

// --- FUNGSI UTAMA: SELECT ITEM ---
function selectItem(customer) {
  selectedCustomer.value = customer;
  
  // Auto-fill form
  form.value.customer_name = customer.company_name ?? customer.customer_name;
  form.value.project_name = customer.project_name;
  form.value.type_project_id = customer.project_type ?? customer.type_project;
  
  isOpen.value = false;
}

// --- FETCH DATA DENGAN BEARER TOKEN ---
async function fetchAllDropdowns() {
  if (!rawToken) {
    console.error("Token tidak ditemukan!");
    return;
  }

  loading.value.typeCustomers = true;
  loading.value.typeReports = true;
  
  const headers = { 
    "Authorization": token, // Sekarang sudah mengandung "Bearer "
    "Accept": "application/json"
  };

  try {
    // Menjalankan semua fetch secara paralel untuk kecepatan
    const [resCust, resRep, resAll] = await Promise.all([
      axios.get(`${apiBase}/alltypecustomers`, { headers }),
      axios.get(`${apiBase}/alltypereports`, { headers }),
      axios.get(`${apiBase}/allvisitedcustomers/${id_user}`, { headers })
    ]);

    typeCustomers.value = resCust.data?.data ?? resCust.data;
    typeReports.value = resRep.data?.data ?? resRep.data;
    
    // Mapping data customers untuk dropdown
    const customerData = resAll.data?.data ?? resAll.data;
    customers.value = Array.isArray(customerData) ? customerData.map(item => ({
      ...item,
      customer_name: item.company_name ?? item.customer_name ?? item.name ?? "Tanpa Nama"
    })) : [];

  } catch (err) {
    console.error("Gagal memuat data:", err.response?.data ?? err.message);
    if (err.response?.status === 401) {
      alert("Sesi berakhir, silakan login kembali.");
    }
  } finally {
    loading.value.typeCustomers = false;
    loading.value.typeReports = false;
  }
}

watch(() => form.value.type_report_id, (val) => {
  if (val !== 5) {
    form.value.nominal_purchase_order = null;
  }
});

// --- SUBMIT FORM ---
async function submitForm() {
  submitting.value = true;
  try {
    const fd = new FormData();
    // Tambahkan data dari form ref
    Object.keys(form.value).forEach(key => {
      if (form.value[key] !== null && form.value[key] !== "") {
        fd.append(key, form.value[key]);
      }
    });
    
    if (selectedCustomer.value?.id) {
      fd.append("customer_id", selectedCustomer.value.id);
    }

    await axios.post(`${apiBase}/sales-reports`, fd, {
      headers: { 
        "Authorization": token,
        "Content-Type": "multipart/form-data" 
      }
    });
    
    alert("Laporan berhasil disimpan!");
    window.location.href = "/";
  } catch (err) {
    console.error("Submit Error:", err.response?.data);
    alert("Gagal menyimpan: " + (err.response?.data?.message ?? "Terjadi kesalahan internal"));
  } finally {
    submitting.value = false;
  }
}

onMounted(fetchAllDropdowns);
</script>

<style scoped>
/* Pastikan teks wrap dan tinggi input VueSelect bisa menyesuaikan */
:deep(.vs__dropdown-toggle) {
min-height: 48px !important;
height: auto !important;
align-items: flex-start !important;
padding-top: 6px !important;
padding-bottom: 6px !important;
flex-wrap: wrap !important;
}

/* Pastikan label yang dipilih bisa multiline */
:deep(.vs__selected) {
white-space: normal !important;
word-break: break-word !important;
line-height: 1.3 !important;
padding-top: 2px !important;
padding-bottom: 2px !important;
}

/* 🟢 PERBAIKAN LEBAR DROPDOWN MENU (Inti Masalah Layout) */
:deep(.vs__dropdown-menu) {
width: 100% !important; /* Batasi lebar dropdown menu sesuai lebar parent (input) */
max-width: 100% !important;
overflow-x: hidden !important; /* Cegah scroll horizontal yang menyebabkan overflow */
overflow-y: auto !important;
max-height: 300px !important; /* Batasi tinggi menu */
}

/* Atur opsi dropdown tetap rapi */
:deep(.vs__dropdown-option) {
white-space: normal !important;
word-break: break-word !important;
line-height: 1.3 !important;
padding: 6px 10px !important;
max-width: 100%; /* Penting untuk menjaga opsi tidak melebihi lebar 100% */
}
select option {
  display: block !important;
  white-space: normal !important;
  word-wrap: break-word !important;
  overflow-wrap: break-word !important;
} 
select {
  line-height: 1.2em !important;
}
</style>


