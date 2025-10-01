<template>
  <div class="bg-gray-100 mx-auto max-w-lg p-6 rounded-2xl border border-gray-300 shadow-xl/50 shadow-gray-400">
    <form @submit.prevent="submitForm" enctype="multipart/form-data" class="space-y-6">
      <h1 class="text-3xl font-bold text-center text-orange-600 mb-1">
        Report <strong class="text-orange-900">Sales</strong> 📊
      </h1>

      <!-- Date -->
      <div>
        <label for="date" class="block text-sm font-medium text-gray-700 mb-1">Tanggal Kunjungan</label>
        <input id="date" type="date" v-model="form.date"
               required
               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-orange-500 transition duration-200" />
        <p v-if="errors.date" class="text-red-600 text-sm mt-1">{{ errors.date[0] }}</p>
      </div>

      <!-- Type Customer -->
      <div>
        <label for="type_customer_id" class="block text-sm font-medium text-gray-700 mb-1">Tipe Pelanggan</label>
        <select id="type_customer_id" v-model="form.type_customer_id" :disabled="loading.typeCustomers"
                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-orange-500 transition duration-200">
          <option value="" disabled>
            <!-- text berubah saat loading / error -->
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

      <div class="grid md:grid-cols-2 md:gap-6">
        <!-- Company (customer_name) -->
        <div>
          <label for="customer_name" class="block text-sm font-medium text-gray-700 mb-1">Nama Perusahaan</label>
          <input id="customer_name" type="text" v-model="form.customer_name" required
                 class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-orange-500 transition duration-200" />
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

      <!-- remaining fields... (same as before) -->
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
                    class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">Dapatkan Lokasi 📍</button>
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
            <button type="button" @click="clearPreview" class="px-3 py-2 bg-gray-200 rounded hover:bg-gray-300">Hapus Preview</button>
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

const props = defineProps({
  coordinate: { type: Array, default: () => [0, 0] },
});
const emit = defineEmits(["update:coordinate"]);

const apiBase = (import.meta.env.VITE_API_BASE_URL ?? "http://localhost:8000") + "/api";
const token = localStorage.getItem("api_token") || localStorage.getItem("token") || "";

// form state
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

// dropdowns + loading / fetch error state
const typeCustomers = ref([]);
const typeProjects = ref([]);
const typeReports = ref([]);
const loading = ref({ typeCustomers: false, typeProjects: false, typeReports: false });
const errorsFetch = ref({ typeCustomers: null, typeProjects: null, typeReports: null });

onMounted(() => {
  fetchAllDropdowns();

  // sync from parent coordinate (if provided)
  if (props.coordinate && Array.isArray(props.coordinate) && props.coordinate.length === 2) {
    form.value.coordinate_check_in = `${props.coordinate[0]}, ${props.coordinate[1]}`;
  }
});

watch(() => props.coordinate, (val) => {
  if (val && Array.isArray(val) && val.length === 2) {
    form.value.coordinate_check_in = `${val[0]}, ${val[1]}`;
  }
});

/**
 * Try a list of endpoint candidates and return first successful array (or [])
 */
async function tryEndpoints(candidates = [], headers = {}) {
  for (const url of candidates) {
    try {
      const res = await axios.get(url, { headers });
      const data = res?.data?.data ?? res?.data;
      // accept either array or object that contains array in .data
      if (Array.isArray(data)) {
        // console.log("Fetched", url, "->", data.length, "items");
        return data;
      }
      // sometimes API returns object with items property
      if (Array.isArray(res?.data?.items)) return res.data.items;
      // if API returns plain array directly in res.data
      if (Array.isArray(res.data)) return res.data;
    } catch (e) {
      console.warn("Endpoint failed:", url, e?.message ?? e);
      // continue to next candidate
    }
  }
  return [];
}

async function fetchAllDropdowns() {
  const headers = token ? { Authorization: `Bearer ${token}` } : {};
  // candidates: adjust these strings to match your API naming if needed
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

  // fetch customers
  loading.value.typeCustomers = true;
  errorsFetch.value.typeCustomers = null;
  typeCustomers.value = await tryEndpoints(customerCandidates, headers);
  if (!typeCustomers.value.length) {
    errorsFetch.value.typeCustomers = "Tidak dapat memuat tipe customer. Periksa endpoint API.";
    // fallback: optionally set minimal options so user can continue
    typeCustomers.value = [
      { id: 1, name: "BCI" },
      { id: 2, name: "End User" },
      { id: 3, name: "Retail" }
    ];
  }
  loading.value.typeCustomers = false;

  // fetch projects
  loading.value.typeProjects = true;
  errorsFetch.value.typeProjects = null;
  typeProjects.value = await tryEndpoints(projectCandidates, headers);
  if (!typeProjects.value.length) {
    errorsFetch.value.typeProjects = "Tidak dapat memuat tipe project. Periksa endpoint API.";
    typeProjects.value = [
      { id: 1, name: "Gudang" },
      { id: 2, name: "Street" },
      { id: 3, name: "Plant" }
    ];
  }
  loading.value.typeProjects = false;

  // fetch reports
  loading.value.typeReports = true;
  errorsFetch.value.typeReports = null;
  typeReports.value = await tryEndpoints(reportCandidates, headers);
  if (!typeReports.value.length) {
    errorsFetch.value.typeReports = "Tidak dapat memuat tipe report. Periksa endpoint API.";
    typeReports.value = [
      { id: 1, name: "PO" },
      { id: 2, name: "Follow Up" },
      { id: 3, name: "Offering" }
    ];
  }
  loading.value.typeReports = false;
}

/* helpers */
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

function getLocation(type = "check_in") {
  if (!navigator.geolocation) {
    alert("Geolocation tidak didukung oleh browser ini.");
    return;
  }
  navigator.geolocation.getCurrentPosition(
    (pos) => {
      const lat = pos.coords.latitude.toFixed(6);
      const lng = pos.coords.longitude.toFixed(6);
      const str = `${lat}, ${lng}`;
      if (type === "check_in") form.value.coordinate_check_in = str;
      else form.value.coordinate_check_out = str;
      emit("update:coordinate", [parseFloat(lat), parseFloat(lng)]);
    },
    (err) => alert("Gagal mendapatkan lokasi: " + err.message)
  );
}

async function submitForm() {
  errors.value = {};
  submitting.value = true;
  try {
    const fd = new FormData();
    fd.append("date", form.value.date ?? "");
    fd.append("check_in", form.value.check_in ?? "");
    fd.append("check_out", form.value.check_out ?? "");
    fd.append("coordinate_check_in", form.value.coordinate_check_in ?? "");
    fd.append("coordinate_check_out", form.value.coordinate_check_out ?? "");
    fd.append("type_customer_id", form.value.type_customer_id ?? "");
    fd.append("customer_name", form.value.customer_name ?? "");
    fd.append("type_project_id", form.value.type_project_id ?? "");
    fd.append("project_name", form.value.project_name ?? "");
    fd.append("pic_name", form.value.pic_name ?? "");
    fd.append("pic_phone", form.value.pic_phone ?? "");
    fd.append("pic_position", form.value.pic_position ?? "");
    fd.append("type_report_id", form.value.type_report_id ?? "");
    fd.append("report_notes", form.value.report_notes ?? "");
    fd.append("equipment_needs", form.value.equipment_needs ?? "");
    fd.append("items_purchase_order", form.value.items_purchase_order ?? "");
    fd.append("nominal_purchase_order", form.value.nominal_purchase_order ?? "");
    if (form.value.picture) fd.append("picture", form.value.picture);

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
    if (r && r.status === 422 && r.data && r.data.errors) {
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
  form.value.date = "";
  form.value.check_in = "";
  form.value.check_out = "";
  form.value.coordinate_check_in = "";
  form.value.coordinate_check_out = "";
  form.value.type_customer_id = "";
  form.value.customer_name = "";
  form.value.type_project_id = "";
  form.value.project_name = "";
  form.value.pic_name = "";
  form.value.pic_phone = "";
  form.value.pic_position = "";
  form.value.type_report_id = "";
  form.value.report_notes = "";
  form.value.equipment_needs = "";
  form.value.items_purchase_order = "";
  form.value.nominal_purchase_order = "";
  form.value.picture = null;
  clearPreview();
}
</script>

<style scoped>
/* minimal safe styles (no @apply) */
.bg-gray-100 { background-color: #f3f4f6; }
.mx-auto { margin-left: auto; margin-right: auto; }
.max-w-lg { max-width: 40rem; }
.p-6 { padding: 1.5rem; }
.rounded-2xl { border-radius: 1rem; }
.border { border-width: 1px; }
.border-gray-300 { border-color: #d1d5db; }
</style>
