<template>
  <div class="bg-gray-100 mx-auto max-w-lg p-6 rounded-2xl border border-gray-300 shadow-xl/50 shadow-gray-400">
    <form @submit.prevent="submitForm" enctype="multipart/form-data" class="space-y-6">
      <h1 class="text-3xl font-bold text-center text-orange-600 mb-1">
        Report <strong class="text-orange-900">Sales</strong> 📊
      </h1>

      <!-- Date -->
      <div>
        <label for="date" class="block text-sm font-medium text-gray-700 mb-1">Date</label>
        <input id="date" type="date" v-model="form.date" required
          class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-orange-500 transition duration-200" />
        <p v-if="errors.date" class="text-red-600 text-sm mt-1">{{ errors.date[0] }}</p>
      </div>

      <!-- Type Customer -->
      <div>
        <label for="type_customer_id" class="block text-sm font-medium text-gray-700 mb-1">Type Customer</label>
        <select id="type_customer_id" v-model="form.type_customer_id" :disabled="loading.typeCustomers"
          class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-orange-500 transition duration-200">
          <option value="" disabled>
            <template v-if="loading.typeCustomers">Memuat...</template>
            <template v-else-if="errorsFetch.typeCustomers">Gagal memuat</template>
            <template v-else>Pilih Tipe Pelanggan</template>
          </option>

          <option v-for="tc in typeCustomers" :key="tc.id" :value="tc.id">
            {{ tc.name }}
          </option>
        </select>
        <p v-if="errors.type_customer_id" class="text-red-600 text-sm mt-1">{{ errors.type_customer_id[0] }}</p>
      </div>


      <!-- Company (customer_name) -->
      <div class="relative">
        <label for="customer_name" class="block text-sm font-medium text-gray-700 mb-1">Nama Perusahaan</label>

        <!-- vue3-select component -->
        <!-- vue3-select component -->
        <VueSelect v-model="selectedCustomer" :options="customers" :get-option-label="formatCustomerLabel"
          placeholder="Pilih atau cari customer..." :loading="loadingCustomers" searchable clearable @search="onSearch"
          class="w-full">
          <!-- no-options slot: beri tombol tambah cepat -->
          <template #no-options>
            <div class="px-3 py-2 text-sm text-gray-600 flex items-center justify-between">
              <div>Tidak ditemukan</div>
              <button type="button" class="text-orange-600 underline ml-2" @click="showCreateInline = true">
                Tambah baru
              </button>
            </div>
          </template>
        </VueSelect>

        <!-- Inline create (opsional) -->
        <div v-if="showCreateInline" class="mt-2 flex gap-2">
          <input type="text" v-model="inlineCustomerName" placeholder="Nama perusahaan baru"
            class="flex-grow px-3 py-2 border border-gray-300 rounded-md" />
          <button type="button" @click="createCustomerInline"
            class="px-3 py-2 bg-green-600 text-white rounded-md">Tambah</button>
          <button type="button" @click="() => (showCreateInline = false)"
            class="px-3 py-2 bg-gray-200 rounded-md">Batal</button>
        </div>

        <p v-if="errors.customer_name" class="text-red-600 text-sm mt-1">{{ errors.customer_name[0] }}</p>
      </div>

      <!-- Type Project -->
      <div>
        <label for="type_project_id" class="block text-sm font-medium text-gray-700 mb-1">Tipe Proyek</label>
        <select id="type_project_id" v-model="form.type_project_id" :disabled="loading.typeProjects"
          class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-orange-500 transition duration-200">
          <option value="" disabled>
            <template v-if="loading.typeProjects">Memuat...</template>
            <template v-else-if="errorsFetch.typeProjects">Gagal memuat</template>
            <template v-else>Pilih Tipe Proyek</template>
          </option>
          <option v-for="tp in typeProjects" :key="tp.id" :value="tp.id">{{ tp.name }}</option>
        </select>
        <p v-if="errors.type_project_id" class="text-red-600 text-sm mt-1">{{ errors.type_project_id[0] }}</p>
      </div>


      <!-- Check In -->
      <div>
        <label for="check_in" class="block text-sm font-medium text-gray-700 mb-1">Time Check In</label>
        <div class="flex gap-2">
          <input id="check_in" type="time" v-model="form.check_in" readonly
            class="flex-grow px-3 py-2 border border-gray-300 rounded-md bg-gray-50 cursor-not-allowed" />
          <button type="button" @click="setCheckIn"
            class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition">Check In</button>
        </div>
        <p v-if="errors.check_in" class="text-red-600 text-sm mt-1">{{ errors.check_in[0] }}</p>
      </div>

      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Koordinat Check In</label>
        <div class="flex flex-col lg:flex-row gap-2">
          <input type="text" v-model="form.coordinate_check_in" readonly
            class="flex-grow px-3 py-2 border border-gray-300 rounded-md bg-gray-50" />
          <button type="button" @click="getLocation('check_in')"
            class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">Dapatkan Lokasi 📍</button>
        </div>
      </div>

      <!-- Project Name -->
      <div>
        <label for="project_name" class="block text-sm font-medium text-gray-700 mb-1">Nama Proyek</label>
        <input id="project_name" type="text" v-model="form.project_name" required
          class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-orange-500 transition duration-200" />
        <p v-if="errors.project_name" class="text-red-600 text-sm mt-1">{{ errors.project_name[0] }}</p>
      </div>

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

      <!-- Type Report -->
      <div>
        <label for="type_report_id" class="block text-sm font-medium text-gray-700 mb-1">Tipe Laporan</label>
        <select id="type_report_id" v-model="form.type_report_id" :disabled="loading.typeReports"
          class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-orange-500 transition duration-200">
          <option value="" disabled>
            <template v-if="loading.typeReports">Memuat...</template>
            <template v-else-if="errorsFetch.typeReports">Gagal memuat</template>
            <template v-else>Pilih Tipe Laporan</template>
          </option>
          <option v-for="tr in typeReports" :key="tr.id" :value="tr.id">{{ tr.name }}</option>
        </select>
        <p v-if="errors.type_report_id" class="text-red-600 text-sm mt-1">{{ errors.type_report_id[0] }}</p>
      </div>

      <!-- Report Notes -->
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

      <div>
        <label for="items_purchase_order" class="block text-sm font-medium text-gray-700 mb-1">Item Purchase</label>
        <textarea id="items_purchase_order" v-model="form.items_purchase_order" rows="3"
          class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-orange-500 transition duration-200"></textarea>
      </div>

      <div>
        <label for="nominal_purchase_order" class="block text-sm font-medium text-gray-700 mb-1">Proyeksi Nilai</label>
        <input id="nominal_purchase_order" type="number" v-model.number="form.nominal_purchase_order"
          class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-orange-500 transition duration-200" />
      </div>

      <!-- Check Out & Coordinates -->
      <div>
        <label for="check_out" class="block text-sm font-medium text-gray-700 mb-1">Time Check Out</label>
        <div class="flex flex-row lg:flex-col gap-2">
          <input id="check_out" type="time" v-model="form.check_out" readonly
            class="flex-grow px-3 py-2 border border-gray-300 rounded-md bg-gray-50 cursor-not-allowed" />
          <button type="button" @click="setCheckOut"
            class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition">Check Out</button>
        </div>
      </div>

      <div class="grid md:grid-cols-1 md:gap-6">
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Koordinat Check Out</label>
          <div class="flex flex-col lg:flex-row gap-2">
            <input type="text" v-model="form.coordinate_check_out" readonly
              class="flex-grow px-3 py-2 border border-gray-300 rounded-md bg-gray-50" />
            <button type="button" @click="getLocation('check_out')"
              class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">Dapatkan Lokasi
              📍</button>
          </div>
        </div>
      </div>

      <!-- Picture -->
      <div>
        <label for="picture" class="block text-sm font-medium text-gray-700 mb-1">Unggah Foto</label>
        <input id="picture" type="file" @change="onFileChange" accept="image/*"
          class="w-full text-sm text-gray-900 border border-gray-300 rounded-md cursor-pointer bg-white file:mr-4 file:py-2 file:px-4 file:rounded-md" />
        <p v-if="errors.picture" class="text-red-600 text-sm mt-1">{{ errors.picture[0] }}</p>

        <div v-if="previewUrl" class="mt-3">
          <img :src="previewUrl" alt="preview" class="w-40 h-40 object-cover rounded-md border" />
          <div class="mt-2">
            <button type="button" @click="clearPreview" class="px-3 py-2 bg-gray-200 rounded hover:bg-gray-300">Hapus
              Preview</button>
          </div>
        </div>
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
import { ref, watch, onMounted } from "vue";
import axios from "axios";
import { Geolocation } from "@capacitor/geolocation";
import VueSelect from "vue3-select";
import "vue3-select/dist/vue3-select.css";

const components = { VueSelect };

const apiBase = (import.meta.env.VITE_API_BASE_URL ?? "http://localhost:8000") + "/api";
const token = localStorage.getItem("api_token") || localStorage.getItem("token") || "";
const role = Number(localStorage.getItem("role") || 0);
const branch = localStorage.getItem("branch") || "";

const form = ref({
  date: "",
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
  nominal_purchase_order: "",
  picture: null,
});

const previewUrl = ref(null);
const errors = ref({});
const submitting = ref(false);

const typeCustomers = ref([]);
const typeProjects = ref([]);
const typeReports = ref([]);
const loading = ref({ typeCustomers: false, typeProjects: false, typeReports: false });
const errorsFetch = ref({ typeCustomers: null, typeProjects: null, typeReports: null });

const customers = ref([]);
const loadingCustomers = ref(false);
const selectedCustomer = ref(null);
const showCreateInline = ref(false);
const inlineCustomerName = ref("");
let searchTimer = null;

function displayCustomerName(c) {
  return c.company_name ?? c.customer_name ?? c.company ?? c.name ?? "";
}

function formatCustomerLabel(option) {
  const company = option.company_name ?? option.customer_name ?? "-";
  const role = option.role_on_project ?? "Unknown";
  const project = option.project_id ?? "N/A";
  return `${company} - ${role} - ${project}`;
}

async function fetchCustomersFromServer(search = "") {
  try {
    loadingCustomers.value = true;
    const headers = token ? { Authorization: `Bearer ${token}` } : {};
    const params = {};
    if (search) params.search = search;
    if (role >= 5 && branch) params.branch = branch;

    const res = await axios.get(`${apiBase}/customerdatabase`, { headers, params });
    const data = (res.data && (res.data.data || res.data)) || [];
    customers.value = data.map(item => ({
      ...item,
      company_name: item.company_name ?? item.customer_name ?? item.company ?? item.name ?? ""
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

watch(selectedCustomer, (val) => {
  if (val) {
    form.value.customer_name = displayCustomerName(val);
  } else {
    form.value.customer_name = "";
  }
});

async function createCustomerInline() {
  const name = (inlineCustomerName.value || "").trim();
  if (!name) {
    alert("Masukkan nama perusahaan untuk ditambahkan.");
    return;
  }
  const newItem = { id: Date.now(), company_name: name };
  customers.value.unshift(newItem);
  selectedCustomer.value = newItem;
  inlineCustomerName.value = "";
  showCreateInline.value = false;
  alert("Perusahaan ditambahkan secara lokal (tidak disimpan ke database).");
}

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
    `${apiBase}/type-customers`,
    `${apiBase}/type_customers`,
    `${apiBase}/typecustomers/all`
  ];
  const projectCandidates = [
    `${apiBase}/alltypeprojects`,
    `${apiBase}/typeprojects`,
    `${apiBase}/type-projects`,
    `${apiBase}/type_projects`
  ];
  const reportCandidates = [
    `${apiBase}/alltypereports`,
    `${apiBase}/typereports`,
    `${apiBase}/type-reports`,
    `${apiBase}/type_reports`
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
}

function setCheckIn() {
  const now = new Date();
  form.value.check_in = now.toTimeString().slice(0, 5);
}
function setCheckOut() {
  const now = new Date();
  form.value.check_out = now.toTimeString().slice(0, 5);
}

async function getLocation(type = "check_in") {
  if (!("geolocation" in navigator)) {
    alert("Geolocation tidak didukung pada perangkat ini.");
    return;
  }
  try {
    const permissionStatus = await Geolocation.requestPermissions();
    if (permissionStatus.location !== "granted") {
      alert("Izin lokasi ditolak.");
      return;
    }
    const pos = await Geolocation.getCurrentPosition({ enableHighAccuracy: true, timeout: 15000 });
    const lat = pos.coords.latitude.toFixed(6);
    const lng = pos.coords.longitude.toFixed(6);
    const str = `${lat}, ${lng}`;
    if (type === "check_in") form.value.coordinate_check_in = str;
    else form.value.coordinate_check_out = str;
  } catch (err) {
    console.error("Gagal mendapatkan lokasi:", err);
    alert("Gagal mendapatkan lokasi: " + (err?.message || ""));
  }
}

/* ✅ Revisi besar di bawah ini */
async function submitForm() {
  errors.value = {};
  submitting.value = true;

  try {
    // Tidak perlu buat customer baru, cukup kirim nama yang diketik user
    const fd = new FormData();
    for (const key in form.value) {
      if (form.value[key] !== null && form.value[key] !== undefined) {
        fd.append(key, form.value[key]);
      }
    }

    // Jika user pilih dari list, sertakan id-nya juga
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
    window.location.href = "/";
  } catch (err) {
    const r = err?.response;
    if (r && r.status === 422 && r.data?.errors) {
      errors.value = r.data.errors;
      window.scrollTo({ top: 0, behavior: "smooth" });
    } else {
      console.error("submit error", err);
      alert(r?.data?.message ?? "Gagal menyimpan laporan");
    }
  } finally {
    submitting.value = false;
  }
}

function resetForm() {
  Object.keys(form.value).forEach(k => (form.value[k] = ""));
  form.value.picture = null;
  clearPreview();
}

onMounted(() => {
  fetchAllDropdowns();
  fetchCustomersFromServer("");
});
</script>

<style scoped>
.vue3-select {
  --vs-border: #d1d5db;
  --vs-border-radius: 0.375rem;
  --vs-color: #374151;
  --vs-border-active: #f97316;
}

/* Tambahan agar teks dalam opsi bisa multi-baris (wrap) */
.vue3-select .vs__dropdown-option,
.vue3-select .vs__selected {
  white-space: normal !important;
  word-wrap: break-word !important;
  line-height: 1.3;
  font-size: 0.875rem; /* opsional: sedikit kecil biar muat */
}

/* Tambahan opsional: agar padding tetap rapi saat teks panjang */
.vue3-select .vs__dropdown-option {
  padding-top: 6px;
  padding-bottom: 6px;
}
</style>

