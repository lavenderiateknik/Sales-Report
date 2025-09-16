<template>
  <div class="bg-gray-100 mx-auto max-w-lg p-6 rounded-2xl border border-gray-300 shadow-xl/50 shadow-gray-400">
    <form @submit.prevent="handleSubmit">
      <h1 class="text-3xl font-bold text-center text-orange-600 mb-6">
        Report <strong class="text-orange-900">Sales</strong> 📊
      </h1>

      <div class="mb-5">
        <label for="dateVisit" class="block text-sm font-medium text-gray-700 mb-1">Tanggal Kunjungan</label>
        <input type="date" id="dateVisit" v-model="form.dateVisit" required
          class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition duration-200">
      </div>

      <div class="mb-5">
        <label for="typeCustomer" class="block text-sm font-medium text-gray-700 mb-1">Tipe Pelanggan</label>
        <select id="typeCustomer" v-model="form.typeCustomer" required
          class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition duration-200">
          <option value="" disabled selected>Pilih Tipe Pelanggan</option>
          <option value="BCI">BCI</option>
          <option value="End User">End User</option>
          <option value="Retail">Retail</option>
        </select>
      </div>
      <div class="mb-5">
        <label for="checkInTime" class="block text-sm font-medium text-gray-700 mb-1">Time Check In</label>
        <div class="flex gap-2">
          <input type="time" id="checkInTime" v-model="form.checkinTime" readonly
            class="flex-grow px-3 py-2 border border-gray-300 rounded-md bg-gray-50 cursor-not-allowed">
          <button type="button" @click="setCheckIn"
            class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition">
            Check In
          </button>
        </div>
      </div>
      <div class="grid md:grid-cols-2 md:gap-6">
        <div class="mb-5">
          <label for="companyName" class="block text-sm font-medium text-gray-700 mb-1">Nama Perusahaan</label>
          <input type="text" id="companyName" v-model="form.company" placeholder="Nama Perusahaan" required
            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition duration-200">
        </div>
        <div class="mb-5">
          <label for="projectType" class="block text-sm font-medium text-gray-700 mb-1">Tipe Proyek</label>
          <select id="projectType" v-model="form.projectType" required
            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition duration-200">
            <option value="" disabled selected>Pilih Tipe Proyek</option>
            <option value="Gudang">Gudang</option>
            <option value="Street">Street</option>
            <option value="Plant">Plant</option>
            <option value="Oil & Gas">Oil & Gas</option>
            <option value="Other">Other</option>
          </select>
        </div>
      </div>

      <div class="mb-5">
        <label for="projectName" class="block text-sm font-medium text-gray-700 mb-1">Nama Proyek</label>
        <input type="text" id="projectName" v-model="form.projectName" placeholder="Nama Proyek" required
          class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition duration-200">
      </div>

      <div class="grid md:grid-cols-2 md:gap-6">
        <div class="mb-5">
          <label for="picName" class="block text-sm font-medium text-gray-700 mb-1">Nama PIC</label>
          <input type="text" id="picName" v-model="form.picName" placeholder="Nama Lengkap PIC" required
            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition duration-200">
        </div>
        <div class="mb-5">
          <label for="picPhone" class="block text-sm font-medium text-gray-700 mb-1">Nomor Telepon PIC</label>
          <input type="tel" id="picPhone" v-model="form.picPhone" placeholder="Contoh: 081234567890" required
            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition duration-200">
        </div>
      </div>

      <div class="grid md:grid-cols-2 md:gap-6">
        <div class="mb-5">
          <label for="picPosition" class="block text-sm font-medium text-gray-700 mb-1">Posisi PIC</label>
          <input type="text" id="picPosition" v-model="form.picPosition" placeholder="Contoh: Manager Proyek" required
            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition duration-200">
        </div>
        <div class="mb-5">
          <label for="reportType" class="block text-sm font-medium text-gray-700 mb-1">Tipe Laporan</label>
          <select id="reportType" v-model="form.reportType" required
            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition duration-200">
            <option value="" disabled selected>Pilih Tipe Laporan</option>
            <option value="PO">PO</option>
            <option value="Follow Up">Follow Up</option>
            <option value="Offering">Offering</option>
          </select>
        </div>
      </div>

      <div class="mb-5">
        <label for="reportNotes" class="block text-sm font-medium text-gray-700 mb-1">Catatan Laporan</label>
        <textarea id="reportNotes" v-model="form.reportNotes" rows="4"
          placeholder="Detail kunjungan, diskusi, dan hasil." required
          class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition duration-200 resize-none"></textarea>
      </div>
      <div class="mb-5">
        <label for="equipmentNeed" class="block text-sm font-medium text-gray-700 mb-1">Kebutuhan Alat</label>
        <textarea type="text" rows="4" id="equipmentNeed" v-model="form.equipmentNeed"
          placeholder="Contoh: Mesin Bor, Genset" required
          class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition duration-200"></textarea>
      </div>
      <div class="mb-5">
        <label for="preorderItem" class="block text-sm font-medium text-gray-700 mb-1">Item PreOrder</label>
        <textarea rows="4" type="text" id="preorderItem" v-model="form.preorderItem"
          placeholder="Contoh: 100 unit Genset" required
          class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition duration-200"></textarea>
      </div>

      <div class="mb-5">
        <label for="projectionValue" class="block text-sm font-medium text-gray-700 mb-1">Proyeksi Nilai</label>
        <input type="text" id="projectionValue" v-model="form.projectionValue" placeholder="Contoh: 15.000.000" required
          class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition duration-200">
      </div>
      <div class="mb-5">
        <label for="checkOutTime" class="block text-sm font-medium text-gray-700 mb-1">Time Check Out</label>
        <div class="flex gap-2">
          <input type="time" id="checkOutTime" v-model="form.checkOutTime" readonly
            class="flex-grow px-3 py-2 border border-gray-300 rounded-md bg-gray-50 cursor-not-allowed">
          <button type="button" @click="setCheckOut"
            class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition">
            Check Out
          </button>
        </div>
      </div>
      <div class="mb-5">
        <label for="coordinateLocation" class="block text-sm font-medium text-gray-700 mb-1">Koordinat Lokasi</label>
        <div class="flex flex-col lg:flex-row items-center gap-2">
          <input type="text" id="coordinateLocation" v-model="form.coordinate" placeholder="Latitude, Longitude"
            required readonly
            class="flex-grow px-3 py-2 border border-gray-300 rounded-md bg-gray-50 cursor-not-allowed">
          <button type="button" @click="getLocation"
            class="text-white whitespace-nowrap bg-blue-600 hover:bg-blue-700 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 transition duration-200">
            Dapatkan Lokasi 📍
          </button>
        </div>
      </div>

      <div class="mb-6">
        <label for="picture" class="block text-sm font-medium text-gray-700 mb-1">Unggah Foto</label>
        <input type="file" id="picture" @change="onFileChange" required
          class="w-full text-sm text-gray-900 border border-gray-300 rounded-md cursor-pointer bg-white file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-gray-200 file:text-gray-700 hover:file:bg-gray-300">
      </div>

      <div class="text-center">
        <button type="submit"
          class="w-full bg-orange-600 hover:bg-orange-700 text-white font-bold py-3 px-4 rounded-md focus:outline-none focus:ring-2 focus:ring-orange-500 focus:ring-offset-2 transition duration-200">
          Kirim Laporan
        </button>
      </div>
    </form>
  </div>
</template>

<script setup>
import { ref, watch } from "vue";

const props = defineProps({
  coordinate: {
    type: Array,
    default: () => [0, 0],
  },
});
const emit = defineEmits(["update:coordinate"]);

const form = ref({
  dateVisit: "",
  typeCustomer: "",
  company: "",
  projectType: "",
  projectName: "",
  picName: "",
  picPhone: "",
  picPosition: "",
  reportType: "",
  reportNotes: "",
  equipmentNeed: "",
  preorderItem: "",
  projectionValue: "",
  checkinTime: "",
  checkOutTime: "",
  coordinate: props.coordinate.join(", "),
  picture: null,
});


const onFileChange = (e) => {
  form.value.picture = e.target.files[0];
};

const getLocation = () => {
  if (navigator.geolocation) {
    navigator.geolocation.getCurrentPosition(
      (pos) => {
        const lat = pos.coords.latitude.toFixed(6);
        const lng = pos.coords.longitude.toFixed(6);
        form.value.coordinate = `${lat}, ${lng}`;
        emit("update:coordinate", [parseFloat(lat), parseFloat(lng)]);
      },
      (err) => {
        alert("Gagal mendapatkan lokasi: " + err.message);
      }
    );
  }
};

const setCheckIn = () => {
  const now = new Date();
  form.value.checkinTime = now.toTimeString().slice(0, 5); // format HH:mm
};

const setCheckOut = () => {
  const now = new Date();
  form.value.checkOutTime = now.toTimeString().slice(0, 5); // format HH:mm
};


const handleSubmit = () => {
  // Lakukan sesuatu dengan data form, misalnya mengirim ke API
  console.log("Form data:", form.value);
};

// Sinkronisasi dari parent ke input
watch(
  () => props.coordinate,
  (val) => {
    form.value.coordinate = val.join(", ");
  }
);
</script>

<style scoped>
/* Anda bisa menambahkan gaya kustom di sini jika diperlukan */
</style>