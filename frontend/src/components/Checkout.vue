<template>
  <div class="bg-gray-100 mx-auto max-w-lg p-6 rounded-2xl border border-gray-300 shadow-xl my-2">
    <form @submit.prevent="updateForm" enctype="multipart/form-data" class="space-y-4">

      <h1 class="text-3xl font-bold text-center text-orange-600 mb-1">
        Report <strong class="text-orange-900">Sales</strong> 📊
      </h1>

      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">
          Tipe Laporan
        </label>
        <input 
          type="text" 
          v-model="form.type_report_name" 
          readonly 
          class="w-full px-3 py-2 border border-gray-300 rounded-md bg-gray-200 cursor-not-allowed"
        />
        <input type="hidden" v-model="form.type_report_id">
      </div>

      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal</label>
        <input
          type="date"
          v-model="form.date"
          class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-orange-500" disabled
        />
        <p v-if="errors.date" class="text-red-600 text-sm mt-1">{{ errors.date[0] }}</p>
      </div>

      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Tipe Customer</label>
        <select v-model="form.type_customer_id" class="w-full px-3 py-2 border border-gray-300 rounded-md" disabled>
          <option :value="1">BCI</option>
          <option :value="2">Non BCI</option>
        </select>
      </div>

      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Nama Perusahaan</label>
        <input
          type="text"
          v-model="form.customer_name"
          readonly
          class="w-full px-3 py-2 border border-gray-300 rounded-md bg-gray-200 cursor-not-allowed"
        />
      </div>

      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Tipe Proyek</label>
        <input type="text" v-model="form.type_project" class="w-full px-3 py-2 border border-gray-300 rounded-md" readonly />
      </div>

      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Nama Proyek</label>
        <input type="text" v-model="form.project_name" class="w-full px-3 py-2 border border-gray-300 rounded-md" />
      </div>

      <div class="grid grid-cols-2 gap-3">
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">PIC Name</label>
          <input type="text" v-model="form.pic_name" placeholder="Nama PIC" class="px-3 py-2 border rounded-md" />
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">PIC Phone</label>
          <input type="text" v-model="form.pic_phone" placeholder="Telp PIC" class="px-3 py-2 border rounded-md" />
        </div>
      </div>
      <label class="block text-sm font-medium text-gray-700 mb-1">PIC Positition</label>
      <input type="text" v-model="form.pic_position" placeholder="Posisi PIC" class="w-full px-3 py-2 border rounded-md" />

      <div class="grid grid-cols-2 gap-3">
        <div>
          <label class="text-xs font-bold">Check In</label>
          <input type="time" v-model="form.check_in" readonly class="w-full px-3 py-2 border bg-gray-200 rounded-md"  />
        </div>
        <div>
          <label class="text-xs font-bold">Koordinat In</label>
          <input type="text" v-model="form.coordinate_check_in" readonly class="w-full px-3 py-2 border bg-gray-200 rounded-md" />
        </div>
      </div>

      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Catatan Laporan</label>
        <textarea v-model="form.report_notes" rows="3" class="w-full px-3 py-2 border rounded-md" />
      </div>
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Kebutuhan Alat</label>
        <textarea v-model="form.equipment_needs" rows="3" class="w-full px-3 py-2 border rounded-md" />
      </div>

      <div class="mb-4">
        <label class="block text-sm font-medium text-gray-700 mb-1">Unggah Foto</label>
        <input type="file" class="w-full" accept="image/*" @change="onFileChange" />
        <div v-if="previewUrl" class="mt-2">
          <img :src="previewUrl" class="h-40 w-full object-cover rounded-md border" />
          <button type="button" class="text-red-500 text-sm mt-1 underline" @click="clearPreview">Hapus Foto</button>
        </div>
      </div>

      <div class="p-4 border border-red-200 rounded-xl bg-red-50 space-y-3">
        <label class="block font-bold text-red-700">Check Out Area</label>
        
        <div class="flex gap-2">
          <input type="time" class="flex-1 px-3 py-2 border rounded-md" v-model="form.check_out" readonly />
          <button type="button" class="bg-red-600 text-white px-4 py-2 rounded-md" @click="handleCheckOut">
            Check Out
          </button>
        </div>

        <div class="flex gap-2">
          <input type="text" class="flex-1 px-3 py-2 border rounded-md" v-model="form.coordinate_check_out" readonly placeholder="Klik Lokasi 📍" />
          <button type="button" class="bg-blue-600 text-white px-4 py-2 rounded-md" @click="getCheckoutLocation">
            📍 Lokasi
          </button>
        </div>
      </div>

      <button
        type="submit"
        :disabled="submitting"
        class="w-full bg-orange-600 hover:bg-orange-700 text-white py-3 rounded-md font-bold transition">
        {{ submitting ? 'Menyimpan...' : 'Check Out' }}
      </button>

    </form>
  </div>
</template>

<script setup>
import { ref, reactive, onMounted } from 'vue'
import { useRoute } from 'vue-router'
import axios from 'axios'

const API_BASE = import.meta.env.VITE_API_BASE_URL || 'http://127.0.0.1:8000'
const route = useRoute()

const submitting = ref(false)
const errors = ref({})
const previewUrl = ref(null)

const form = reactive({
  id: null,
  type_report_id: '',
  type_report_name: '', // Untuk tampilan varchar
  date: '',
  type_customer_id: '',
  customer_name: '',
  type_project: '',
  project_name: '',
  pic_name: '',
  pic_phone: '',
  pic_position: '',
  check_in: '',
  coordinate_check_in: '',
  report_notes: '',
  equipment_needs: '',
  check_out: '',
  coordinate_check_out: '',
  picture: null,
})

function authHeader() {
  const token = localStorage.getItem('api_token')
  return { Authorization: `Bearer ${token}` }
}

async function loadReport() {
  try {
    const res = await axios.get(`${API_BASE}/api/salesreport/${route.params.id}`, { headers: authHeader() })
    const record = res.data.data
    console.log(res)

    // Mapping manual untuk memastikan semua field terisi
    form.id = record.id
    form.date = record.date
    form.type_report_id = record.type_report_id
    form.type_report_name = record.type_report?.name || 'N/A'
    form.type_customer_id = record.type_customer_id
    form.customer_name = record.customer_name
    form.type_project = record.type_project
    form.project_name = record.project_name
    form.pic_name = record.pic_name
    form.pic_phone = record.pic_phone
    form.pic_position = record.pic_position
    form.check_in = record.check_in
    form.coordinate_check_in = record.coordinate_check_in
    form.report_notes = record.report_notes
    form.equipment_needs = record.equipment_needs
    form.check_out = record.check_out
    form.coordinate_check_out = record.coordinate_check_out
    
    // Jika ada gambar dari server, bisa diset ke preview (opsional)
    if(record.picture) {
        previewUrl.value = `${API_BASE}/storage/${record.picture}`
    }

  } catch (err) {
    console.error(err)
    alert('Gagal memuat data')
  }
}

async function updateForm() {
  submitting.value = true
  errors.value = {}

  // Validasi manual di frontend sebelum kirim (Opsional tapi membantu)
  if (!form.check_out || !form.coordinate_check_out) {
    alert('Silakan lakukan Check Out dan ambil Lokasi terlebih dahulu!')
    submitting.value = false
    return
  }

  try {
    const fd = new FormData()
    
    // 1. Beritahu Laravel ini adalah PUT request meskipun dikirim via POST
    fd.append('_method', 'PUT') 

    // 2. Masukkan semua data form ke FormData
    Object.keys(form).forEach(key => {
      // Kita kirim semua data, jika null kirim string kosong agar divalidasi backend
      const value = form[key] === null ? '' : form[key]
      fd.append(key, value)
    })

    // 3. Gunakan POST (karena axios.put dengan FormData + File sering bermasalah di PHP)
    await axios.post(`${API_BASE}/api/checkout/${form.id}`, fd, {
      headers: { 
        ...authHeader(), 
        'Content-Type': 'multipart/form-data' 
      }
    })

    alert('Berhasil Check Out! ✅')
    
    window.location.href = "/optionvisit";
  } catch (err) {
    if (err.response?.status === 422) {
      console.log("Validation Errors:", err.response.data.errors);
      errors.value = err.response.data.errors
    } else {
      console.error(err)
      alert('Terjadi kesalahan sistem')
    }
  } finally {
    submitting.value = false
  }
}

function onFileChange(e) {
  const file = e.target.files[0]
  if (!file) return
  form.picture = file
  previewUrl.value = URL.createObjectURL(file)
}

function clearPreview() {
  form.picture = null
  previewUrl.value = null
}

function handleCheckOut() {
  form.check_out = new Date().toLocaleTimeString('it-IT').slice(0, 5)
}

function getCheckoutLocation() {
  if (!navigator.geolocation) return alert('Geo tidak didukung')
  navigator.geolocation.getCurrentPosition(pos => {
    form.coordinate_check_out = `${pos.coords.latitude}, ${pos.coords.longitude}`
  })
}

onMounted(loadReport)
</script>