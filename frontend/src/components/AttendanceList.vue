<template>
  <div class="mx-2 my-2 p-6 space-y-6 bg-slate-200 rounded-2xl">
    <h1 class="text-2xl font-bold">📅 Attendance Sales</h1>

    <!-- FILTER -->
    <div class="flex flex-wrap gap-3 items-center">
      <div class="flex items-center gap-2">
        <label class="font-semibold">Bulan</label>
        <select v-model="month" @change="loadData" class="border rounded p-2">
          <option v-for="m in months" :key="m.value" :value="m.value">
            {{ m.label }}
          </option>
        </select>
      </div>

      <div class="flex items-center gap-2">
        <label class="font-semibold">Tahun</label>
        <select v-model="year" @change="loadData" class="border rounded p-2">
          <option v-for="y in years" :key="y" :value="y">{{ y }}</option>
        </select>
      </div>
    </div>

    <!-- GRID CARD -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
      <div
        v-for="s in sales"
        :key="s.id"
        class="bg-white rounded-2xl shadow p-4 space-y-3"
      >
        <div class="flex items-center justify-between">
          <h3 class="font-bold text-lg">{{ s.name }}</h3>
          <span class="text-xs px-2 py-1 rounded bg-blue-100 text-blue-700">
            {{ s.branch?.name ?? '-' }}
          </span>
        </div>

        <div class="text-sm space-y-1 text-gray-600">
          <p>🗓 Hari Kerja: <b>{{ s.attendance?.working_days ?? 0 }}</b></p>
          <p>✅ Hadir: <b>{{ s.attendance?.present_days ?? 0 }}</b></p>
          <p>❌ Absen: <b>{{ s.attendance?.absent_days ?? 0 }}</b></p>
        </div>

        <button
          class="w-full mt-2 py-2 rounded bg-blue-600 text-white text-sm"
          @click="goToInput(s)"
        >
          Input / Edit Attendance
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import axios from 'axios'
import { useRouter } from 'vue-router'

/* CONFIG */
const apiBaseUrl = import.meta.env.VITE_API_BASE_URL
const token  = localStorage.getItem("api_token")
const role   = Number(localStorage.getItem("role"))
const branch = localStorage.getItem("branch")

const router = useRouter()

/* STATE */
const years = [2024,2025,2026,2027]
const year  = ref(new Date().getFullYear())
const month = ref(new Date().getMonth()+1)

const months = [
  { value:1,label:'Januari' },{ value:2,label:'Februari' },
  { value:3,label:'Maret' },{ value:4,label:'April' },
  { value:5,label:'Mei' },{ value:6,label:'Juni' },
  { value:7,label:'Juli' },{ value:8,label:'Agustus' },
  { value:9,label:'September' },{ value:10,label:'Oktober' },
  { value:11,label:'November' },{ value:12,label:'Desember' },
]

const sales = ref([])

/* AXIOS */
const api = axios.create({
  baseURL: apiBaseUrl,
  headers:{
    Authorization: token ? `Bearer ${token}` : undefined,
    Accept:'application/json'
  }
})

/* LOAD DATA */
const loadData = async () => {
  try {
    let users = []

    // role filter
    if ([1,2,3].includes(role)) {
      const res = await api.get('/api/allusers')
      users = res.data.data.filter(u => u.role_id === 7)
    } else {
      const res = await api.get(`/api/usersbranch/${branch}`)
      users = res.data.data.filter(u => u.role_id === 7)
    }

    // ambil attendance tiap sales
    for (const u of users) {
      try {
        const a = await api.get(
          `/api/attendance-summaries/${u.id}/${month.value}/${year.value}`
        )
        u.attendance = a.data.data ?? null
      } catch {
        u.attendance = null
      }
    }

    sales.value = users
  } catch (e) {
    console.error('Gagal load attendance', e)
  }
}

/* NAVIGASI */
const goToInput = (s) => {
  router.push({
    name:'attendance-form',
    params:{ userId:s.id },
    query:{ month:month.value, year:year.value }
  })
}

onMounted(loadData)
</script>
