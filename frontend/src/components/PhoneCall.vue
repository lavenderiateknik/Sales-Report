<template>
  <div class="bg-gray-100 mx-auto max-w-lg p-6 rounded-2xl border border-gray-300 shadow-xl/50 shadow-gray-400">
    <form @submit.prevent="submitForm" enctype="multipart/form-data" class="space-y-4">
      <h1 class="text-3xl font-bold text-center text-orange-600 mb-1">
        Report <strong class="text-orange-900">Sales</strong> 📊
      </h1>

      <!-- Type Report -->
      <div>
        <label for="type_report_id" class="block text-sm font-medium text-gray-700 mb-1">Tipe Laporan</label>
        <select id="type_report_id" v-model="form.type_report_id" :disabled="loading.typeReports"
          class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-orange-500 transition duration-200">
          <option value="" disabled>
            {{ loading.typeReports ? 'Memuat...' : (errorsFetch.typeReports ? 'Gagal memuat' : 'Pilih Tipe Laporan') }}
          </option>
          <option v-for="tr in typeReports.filter(t => t.id === 6)" :value="tr.id">{{ tr.name }}</option>
        </select>
        <p v-if="errors.type_report_id" class="text-red-600 text-sm mt-1">{{ errors.type_report_id[0] }}</p>
      </div>


      <!-- Date -->
      <div>
        <label for="date" class="block text-sm font-medium text-gray-700 mb-1">Date</label>
        <input id="date" type="date" v-model="form.date" required :max="today" :min="today"
          class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-orange-500 transition duration-200" />
        <p v-if="errors.date" class="text-red-600 text-sm mt-1">{{ errors.date[0] }}</p>
      </div>

      <!-- Type Customer -->
      <div>
        <label for="type_customer_id" class="block text-sm font-medium text-gray-700 mb-1">Type Customer</label>
        <select id="type_customer_id" v-model="form.type_customer_id" :disabled="loading.typeCustomers"
          class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-orange-500 transition duration-200">
          <option value="" disabled>
            {{ loading.typeCustomers ? 'Memuat...' : (errorsFetch.typeCustomers ? 'Gagal memuat' : 'Pilih Tipe Pelanggan') }}
          </option>
          <option v-for="tc in typeCustomers.filter(tc => tc.id === 2)" :key="tc.id" :value="tc.id">{{ tc.name }}</option>
          
        </select>
        <p v-if="errors.type_customer_id" class="text-red-600 text-sm mt-1">{{ errors.type_customer_id[0] }}</p>
      </div>

      <!-- Customer Name -->
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Nama Perusahaan</label>

        <template v-if="form.type_customer_id == BCI_TYPE_ID" class=" h-11">
        <VueSelect
          v-model="selectedCustomer"
          :options="customers"
          :get-option-label="option => option.isNew ? option.company_name : formatCustomerLabel(option)"
          placeholder="Pilih atau cari customer database..."
          :loading="loadingCustomers"
          searchable
          clearable
          taggable
          @tag="handleTag"
          @search="onSearch"
          class="w-full  rounded-md"
          :class="{'border border-red-500': errors.customer_name}"
        />
        </template>

        <template v-else-if="form.type_customer_id">
          <input
            id="customer_name_input"
            type="text"
            v-model="form.customer_name"
            required
            placeholder="Masukkan nama perusahaan"
            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-orange-500 transition duration-200"
            :class="{'border-red-500': errors.customer_name}"
          />
        </template>

        <template v-else>
          <div class="px-3 py-2 border border-gray-300 rounded-md bg-gray-200 text-gray-500">
            Pilih Tipe Pelanggan terlebih dahulu di atas.
          </div>
        </template>

        <p v-if="errors.customer_name" class="text-red-600 text-sm mt-1">{{ errors.customer_name[0] }}</p>
      </div>
      <!-- Type Project -->
      <div>
        <label for="type_project_id" class="block text-sm font-medium text-gray-700 mb-1">Tipe Proyek</label>

      <!-- BCI: show select built from customers.project_type -->
      <select
        v-if="form.type_customer_id == BCI_TYPE_ID"
        id="type_project_id"
        v-model="form.type_project_id"
        :disabled="loadingCustomers" 
        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-orange-500 transition duration-200"
        :class="{ 'border-red-500': errors.type_project_id }"
        >
        <option value="" disabled>
        {{ loadingCustomers ? 'Memuat...' : (errorsFetch.typeProjects ? 'Gagal memuat' : 'Pilih Tipe Proyek') }}
        </option>
          <option v-for="(pt, idx) in filteredProjectTypes" :key="pt + idx" :value="pt" class=" text-wrap whitespace-normal" >{{ pt }}</option>
      </select>

      <!-- Non-BCI: manual input -->
      <input
        v-else-if="form.type_customer_id"
        id="type_project_id"
        type="text"
        v-model="form.type_project_id"
        placeholder="Masukkan tipe proyek"
        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-orange-500 transition duration-200"
        :class="{ 'border-red-500': errors.type_project_id }"
      />

      <!-- not selected customer type yet -->
      <div v-else class="px-3 py-2 border border-gray-300 rounded-md bg-gray-200 text-gray-500">
        Pilih Tipe Pelanggan terlebih dahulu di atas.
      </div>

      <p v-if="errors.type_project_id" class="text-red-600 text-sm mt-1">{{ errors.type_project_id[0] }}</p>
      </div>

      <!-- Check In -->
      <!-- <div>
        <label for="check_in" class="block text-sm font-medium text-gray-700 mb-1">Time Check In</label>
        <div class="flex gap-2">
          <input id="check_in" type="time" v-model="form.check_in" readonly
            class="flex-grow px-3 py-2 border border-gray-300 rounded-md bg-gray-50 cursor-not-allowed" />
          <button type="button" @click="setCheckIn"
            class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition">Check In</button>
        </div>
        <p v-if="errors.check_in" class="text-red-600 text-sm mt-1">{{ errors.check_in[0] }}</p>
      </div> -->

      <!-- Coordinates Check In -->
      <!-- <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Koordinat Check In</label>
        <div class="flex flex-col lg:flex-row gap-2">
          <input type="text" v-model="form.coordinate_check_in" readonly
            class="flex-grow px-3 py-2 border border-gray-300 rounded-md bg-gray-50" />
          <button type="button" @click="getLocation('check_in')"
            class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">Dapatkan Lokasi 📍</button>
        </div>
      </div> -->

      <!-- Project Name -->
      <div>
        <label for="project_name" class="block text-sm font-medium text-gray-700 mb-1">Nama Proyek</label>
        <input id="project_name" type="text" v-model="form.project_name" required
          class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-orange-500 transition duration-200" />
        <p v-if="errors.project_name" class="text-red-600 text-sm mt-1">{{ errors.project_name[0] }}</p>
      </div>

      <!-- PIC Info -->
      <div class="grid md:grid-cols-2 md:gap-6">
        <div>
          <label for="pic_name" class="block text-sm font-medium text-gray-700 mb-1">Nama PIC</label>
          <input id="pic_name" type="text" v-model="form.pic_name" required
            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-orange-500 transition duration-200" />
          <p v-if="errors.pic_name" class="text-red-600 text-sm mt-1">{{ errors.pic_name[0] }}</p>
        </div>

        <div>
          <label for="pic_phone" class="block text-sm font-medium text-gray-700 mb-1">Nomor Telepon PIC</label>
          <input id="pic_phone" type="tel" v-model="form.pic_phone" required placeholder="081234567890"
            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-orange-500 transition duration-200" />
          <p v-if="errors.pic_phone" class="text-red-600 text-sm mt-1">{{ errors.pic_phone[0] }}</p>
        </div>
      </div>

      <div>
        <label for="pic_position" class="block text-sm font-medium text-gray-700 mb-1">Posisi PIC</label>
        <input id="pic_position" type="text" v-model="form.pic_position" required
          class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-orange-500 transition duration-200" />
        <p v-if="errors.pic_position" class="text-red-600 text-sm mt-1">{{ errors.pic_position[0] }}</p>
      </div>

      <!-- Notes & Equipment -->
      <div>
        <label for="report_notes" class="block text-sm font-medium text-gray-700 mb-1">Catatan Laporan</label>
        <textarea id="report_notes" v-model="form.report_notes" rows="4"
          class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-orange-500 transition duration-200 resize-none"></textarea>
      </div>

      <div>
        <label for="equipment_needs" class="block text-sm font-medium text-gray-700 mb-1">Kebutuhan Alat</label>
        <textarea id="equipment_needs" v-model="form.equipment_needs" rows="3"
          class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-orange-500 transition duration-200"></textarea>
      </div>


      <!-- Submit -->
      <div class="text-center">
        <button type="submit" :disabled="submitting"
          class="w-full bg-orange-600 hover:bg-orange-700 text-white font-bold py-3 px-4 rounded-md focus:outline-none focus:ring-2 focus:ring-orange-500 focus:ring-offset-2 transition duration-200">
          {{ submitting ? 'Menyimpan...' : 'Kirim Laporan' }}
        </button>
      </div>
    </form>
  </div>
</template>

<script setup>
import { ref, watch, onMounted, computed } from "vue";
import axios from "axios";
import { Geolocation } from "@capacitor/geolocation"; 
import VueSelect from "vue3-select";
import "vue3-select/dist/vue3-select.css";


const today = new Date().toISOString().split("T")[0];
// --- 🔸 PROPS & EMIT UNTUK SINKRONISASI DENGAN PARENT ---
const props = defineProps({
  coordinate: {
    type: Array,
    default: () => [-6.175869, 106.823055], // Default Jakarta
  },
});
const emit = defineEmits(["update:coordinate"]);

// --- KONSTANTA ---
const BCI_TYPE_ID = 1;
const apiBase = (import.meta.env.VITE_API_BASE_URL ?? "http://localhost:8000") + "/api";
const token = localStorage.getItem("api_token") || localStorage.getItem("token") || "";
const role = Number(localStorage.getItem("role") || 0);
const branch = localStorage.getItem("branch") || "";

// --- STATE DATA DAN FORM ---
const form = ref({
  date: new Date().toISOString().split("T")[0],
  check_in: "",
  check_out: "",
  coordinate_check_in: "",
  coordinate_check_out: "",
  type_customer_id: "",
  customer_name: "",
  type_project_id: "",
  project_name: "",
  pic_name: "",
  pic_phone: "",
  pic_position: "",
  type_report_id: "",
  report_notes: "",
  equipment_needs: "",
  items_purchase_order: "",
  nominal_purchase_order: null,
  picture: null,
});

// --- UI STATE ---
const previewUrl = ref(null);
const errors = ref({});
const submitting = ref(false);

// --- DROPDOWN STATES ---
const typeCustomers = ref([]);
const typeProjects = ref([]);
const typeReports = ref([]);
const loading = ref({ typeCustomers: false, typeProjects: false, typeReports: false });
const errorsFetch = ref({ typeCustomers: null, typeProjects: null, typeReports: null });

// --- CUSTOMER (VueSelect) ---
const customers = ref([]);
const loadingCustomers = ref(false);
const selectedCustomer = ref(null);
let searchTimer = null;

// --- HELPER FUNGI ---
function displayCustomerName(c) {
  return c.company_name ?? c.customer_name ?? c.company ?? c.name ?? "";
}

function formatCustomerLabel(option) {
  const parts = [option.company_name, option.project_name, option.project_id].filter(Boolean);
  return parts.join(" - ");
}

// --- FETCH DATA DROPDOWNS ---
async function tryEndpoints(candidates = []) {
  const headers = token ? { Authorization: `Bearer ${token}` } : {};
  for (const url of candidates) {
    try {
      const r = await axios.get(url, { headers });
      const d = r?.data?.data ?? r?.data;
      if (Array.isArray(d)) return d;
      if (Array.isArray(r?.data?.items)) return r.data.items;
      if (Array.isArray(r.data)) return r.data;
    } catch (e) {}
  }
  return [];
}

async function fetchAllDropdowns() {
  const customerCandidates = [
    `${apiBase}/alltypecustomers`,
    `${apiBase}/typecustomers`,
    `${apiBase}/type_customers`,
    `${apiBase}/typecustomers/all`,
  ];
  const projectCandidates = [
    `${apiBase}/alltypeprojects`,
    `${apiBase}/typeprojects`,
    `${apiBase}/type_projects`,
  ];
  const reportCandidates = [
    `${apiBase}/alltypereports`,
    `${apiBase}/typereports`,
    `${apiBase}/type_reports`,
  ];

  loading.value.typeCustomers = true;
  typeCustomers.value = await tryEndpoints(customerCandidates);
  if (!typeCustomers.value.length) typeCustomers.value = [{ id: 1, name: "BCI" }, { id: 2, name: "End User" }];
  loading.value.typeCustomers = false;

  loading.value.typeProjects = true;
  typeProjects.value = await tryEndpoints(projectCandidates);
  if (!typeProjects.value.length) typeProjects.value = [{ id: 1, name: "Gudang" }, { id: 2, name: "Street" }];
  loading.value.typeProjects = false;

  loading.value.typeReports = true;
  typeReports.value = await tryEndpoints(reportCandidates);
  if (!typeReports.value.length) typeReports.value = [{ id: 1, name: "PO" }, { id: 2, name: "Follow Up" }];
  loading.value.typeReports = false;
}

// --- FETCH CUSTOMERS ---
async function fetchCustomersFromServer(search = "") {
  try {
    loadingCustomers.value = true;
    const headers = token ? { Authorization: `Bearer ${token}` } : {};
    const params = {};
    if (search) params.search = search;
    if (role >= 5 && branch) params.branch = branch;

    const res = await axios.get(`${apiBase}/allcustomerdatabase`, { headers, params });
    const data = (res.data && (res.data.data || res.data)) || [];

    customers.value = data.map((item) => ({
      ...item,
      company_name: item.company_name ?? item.customer_name ?? item.company ?? item.name ?? "",
      project_name: item.project_name,
      project_id: item.project_id,
      project_type: item.project_type,
    }));
  } catch (err) {
    console.error("Gagal fetch customers:", err);
    customers.value = [];
  } finally {
    loadingCustomers.value = false;
  }
}

function onSearch(searchTerm) {
  clearTimeout(searchTimer);
  searchTimer = setTimeout(() => {
    fetchCustomersFromServer(searchTerm || "");
  }, 300);
}

function handleTag(newTagName) {
  const newCustomer = {
    id: null,
    company_name: newTagName,
    isNew: true,
  };
  customers.value.push(newCustomer);
  selectedCustomer.value = newCustomer;
}

// --- BUTTON & INPUT HANDLER ---
function setCheckIn() {
  const now = new Date();
  form.value.check_in = now.toTimeString().slice(0, 5);
}

function setCheckOut() {
  const now = new Date();
  form.value.check_out = now.toTimeString().slice(0, 5);
}

// --- 📍 GET KOORDINAT & SINKRONISASI DENGAN MAP ---
async function getLocation(type = "check_in") {
  try {
    const isNative = !!window.Capacitor?.isNativePlatform?.();
    let lat, lng;

    if (isNative) {
      const perm = await Geolocation.requestPermissions();
      if (perm.location !== "granted") {
        alert("Izin lokasi ditolak.");
        return;
      }
      const pos = await Geolocation.getCurrentPosition({
        enableHighAccuracy: true,
        timeout: 15000,
      });
      lat = pos.coords.latitude;
      lng = pos.coords.longitude;
    } else {
      if (!("geolocation" in navigator)) {
        alert("Geolocation tidak didukung di browser ini.");
        return;
      }
      await new Promise((resolve, reject) => {
        navigator.geolocation.getCurrentPosition(
          (pos) => {
            lat = pos.coords.latitude;
            lng = pos.coords.longitude;
            resolve();
          },
          (err) => reject(err),
          { enableHighAccuracy: true, timeout: 15000 }
        );
      });
    }

    const str = `${lat.toFixed(6)}, ${lng.toFixed(6)}`;
    if (type === "check_in") form.value.coordinate_check_in = str;
    else form.value.coordinate_check_out = str;

    // 🟢 Kirim ke parent agar Map ikut update
    emit("update:coordinate", [lat, lng]);
  } catch (err) {
    console.error("Gagal mendapatkan lokasi:", err);
    alert("Gagal mendapatkan lokasi: " + (err?.message || "Tidak diketahui"));
  }
}

// --- FILE HANDLER ---
function onFileChange(e) {
  errors.value.picture = null;
  const f = e.target.files?.[0] ?? null;
  if (!f) {
    form.value.picture = null;
    clearPreview();
    return;
  }
  form.value.picture = f;
  clearPreview();
  previewUrl.value = URL.createObjectURL(f);
}

function clearPreview() {
  if (previewUrl.value) {
    URL.revokeObjectURL(previewUrl.value);
    previewUrl.value = null;
  }
  form.value.picture = null;
  const fileInput = document.getElementById("picture");
  if (fileInput) fileInput.value = "";
}

// --- RESET & SUBMIT ---
function resetForm() {
  form.value = {
    date: new Date().toISOString().split("T")[0],
    check_in: "",
    check_out: "",
    coordinate_check_in: "",
    coordinate_check_out: "",
    type_customer_id: "",
    customer_name: "",
    type_project_id: "",
    project_name: "",
    pic_name: "",
    pic_phone: "",
    pic_position: "",
    type_report_id: "",
    report_notes: "",
    equipment_needs: "",
    items_purchase_order: "",
    nominal_purchase_order: null,
    picture: null,
  };
  selectedCustomer.value = null;
  errors.value = {};
  clearPreview();
}

async function submitForm() {
  errors.value = {};
  if (submitting.value) return;
  submitting.value = true;

  try {
    const fd = new FormData();
    for (const key in form.value) {
      if (form.value[key] !== null && form.value[key] !== undefined && form.value[key] !== "") {
        fd.append(key, form.value[key]);
      }
    }

    if (selectedCustomer.value?.id) {
      fd.append("customer_id", selectedCustomer.value.id);
    }

    const headers = {
      "Content-Type": "multipart/form-data",
      ...(token ? { Authorization: `Bearer ${token}` } : {}),
    };

    const res = await axios.post(`${apiBase}/sales-reports`, fd, { headers });
    alert(res.data?.message ?? "Laporan berhasil disimpan");
    resetForm();
    window.location.href = "/optionreport";
  } catch (err) {
    const r = err?.response;
    if (r && r.status === 422 && r.data?.errors) {
      console.log("Validation Errors:", err.response.data.errors);
      errors.value = r.data.errors;
      window.scrollTo({ top: 0, behavior: "smooth" });
    } else {
      console.error("submit error", err);
      console.log("Validation Errors:", err.response.data.errors);
      alert(r?.data?.message ?? "Gagal menyimpan laporan");
    }
  } finally {
    submitting.value = false;
  }
}

// --- WATCHERS ---
watch(selectedCustomer, (val) => {
  form.value.customer_name = val ? displayCustomerName(val) : "";
});

watch(() => form.value.type_customer_id, () => {
  form.value.customer_name = "";
  selectedCustomer.value = null;
});

// 🟢 Sinkronisasi dari parent → form
watch(
  () => props.coordinate,
  (newVal) => {
    if (Array.isArray(newVal) && newVal.length === 2) {
      form.value.coordinate_check_in = `${newVal[0]}, ${newVal[1]}`;
    }
  }
);

const filteredProjectTypes = computed(() => {
  const seen = new Set();
  const out = [];
  for (const c of customers.value) {
    const v = (c.project_type ?? "").toString().trim();
    if (!v) continue;
    if (!seen.has(v)) {
      seen.add(v);
      out.push(v);
    }
  }
  return out;
}); 

watch(selectedCustomer, (val) => {
  if (val && (val.project_type ?? "").toString().trim() !== "") {
    // isi form.type_project_id dengan project_type dari customer
    form.value.type_project_id = val.project_type;
  } else {
    // jika tidak ada project_type pada customer, kosongkan (BIAR JELAS)
    if (form.value.type_customer_id == BCI_TYPE_ID) {
      form.value.type_project_id = "";
    }
  }
});

// saat ganti type_customer, reset field yang relevan
watch(() => form.value.type_customer_id, (val) => {
  // jika bukan BCI, biarkan pengguna mengetik; jika BCI, reset dan pastikan kita punya daftar
  if (Number(val) === BCI_TYPE_ID) {
    // kosongkan agar user memilih dari select (kecuali selectedCustomer sudah mengisi watch di atas)
    if (!form.value.type_project_id) {
      form.value.type_project_id = "";
    }
    // pastikan customers sudah ter-load (fetchCustomersFromServer dipanggil di onMounted)
  } else {
    // non BCI -> gunakan input manual, kosongkan nilai select
    form.value.type_project_id = "";
  }
});

// --- LIFECYCLE ---
onMounted(() => {
  fetchAllDropdowns();
  fetchCustomersFromServer("");
});
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


