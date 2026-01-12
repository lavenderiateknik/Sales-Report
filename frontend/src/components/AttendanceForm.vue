<template>
  <div class="my-2 px-4 py-2 space-y-4 bg-[#10375C]/10 mx-1 rounded-2xl">
    <div class="p-6 bg-white rounded-2xl shadow space-y-6">

      <div class="bg-blue-50 p-3 rounded-xl">
        👤 <b>{{ salesName }}</b>
        <span class="text-sm text-gray-500">
          Attendance {{ monthLabel }} {{ year }}
        </span>
      </div>

      <h2 class="text-xl font-bold">📅 Input Attendance Sales</h2>

      <!-- FORM -->
      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
          <label class="font-semibold text-sm">Hari Kerja</label>
          <input type="number" v-model="form.working_days"
            class="w-full border rounded p-2 mt-1" />
        </div>

        <div>
          <label class="font-semibold text-sm">Hari Hadir</label>
          <input type="number" v-model="form.present_days"
            class="w-full border rounded p-2 mt-1" />
        </div>

        <div>
          <label class="font-semibold text-sm">Hari Absen</label>
          <input type="number" v-model="form.absent_days"
            class="w-full border rounded p-2 mt-1" />
          <p class="text-xs text-gray-400">
            Kosongkan jika ingin dihitung otomatis
          </p>
        </div>
      </div>

      <!-- ACTION -->
      <div class="flex justify-end gap-2">
        <button class="px-4 py-2 rounded bg-gray-200" @click="resetForm">
          Reset
        </button>

        <button
          class="px-4 py-2 rounded bg-blue-600 text-white"
          :disabled="loading"
          @click="submit"
        >
          {{ loading ? 'Menyimpan...' : isEdit ? 'Update Attendance' : 'Simpan Attendance' }}
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue'
import axios from 'axios'
import { useRoute, useRouter } from 'vue-router'

/* ROUTE */
const route = useRoute()
const router = useRouter()

/* CONFIG */
const apiBaseUrl = import.meta.env.VITE_API_BASE_URL
const token = localStorage.getItem('api_token')

const userId = ref(route.params.userId)
const month  = ref(Number(route.query.month))
const year   = ref(Number(route.query.year))

/* STATE */
const loading = ref(false)
const isEdit  = ref(false)
const salesName = ref('')

const form = ref({
  user_id: userId.value,
  month: month.value,
  year: year.value,
  working_days: 0,
  present_days: 0,
  absent_days: null,
})

/* AXIOS */
const api = axios.create({
  baseURL: apiBaseUrl,
  headers:{
    Authorization: token ? `Bearer ${token}` : undefined,
    Accept:'application/json'
  }
})

/* LABEL */
const months = [
  'Januari','Februari','Maret','April','Mei','Juni',
  'Juli','Agustus','September','Oktober','November','Desember'
]
const monthLabel = computed(() => months[month.value-1])

/* LOAD DETAIL */
const loadDetail = async () => {
  try {
    // ambil nama sales
    const u = await api.get(`/api/user/${userId.value}`)
    salesName.value = u.data.data.name
    
    // ambil attendance jika ada
    try {
      const a = await api.get(
        `/api/attendance-summaries/${userId.value}/${month.value}/${year.value}`
      )
      if (a.data.data) {
        form.value = { ...a.data.data }
        isEdit.value = true
      }
    } catch {}
  } catch(e){
    console.error(e)
  }
}

/* SUBMIT */
const submit = async () => {
  loading.value = true
  try {
    await api.post('/api/attendance-summaries', form.value)
    alert(isEdit.value ? 'Attendance diperbarui' : 'Attendance disimpan')
    router.back()
  } catch(e){
    alert('Gagal menyimpan attendance')
  } finally {
    loading.value = false
  }
}

const resetForm = () => {
  form.value.working_days = 0
  form.value.present_days = 0
  form.value.absent_days  = null
}

onMounted(loadDetail)
</script>
