<template>
  <div class="mx-2 my-2 p-6 space-y-6 bg-slate-200 rounded-2xl">
    <h1 class="text-2xl font-bold">🎯 Target KPI Sales</h1>

    <!-- FILTER -->
    <div class="flex flex-wrap gap-3 items-center">
      <div class="flex items-center gap-2">
        <label class="font-semibold">Bulan</label>
        <select v-model="month" @change="loadTargets"
          class="border rounded p-2">
          <option v-for="m in months" :key="m.value" :value="m.value">
            {{ m.label }}
          </option>
        </select>
      </div>

      <div class="flex items-center gap-2">
        <label class="font-semibold">Tahun</label>
        <select v-model="year" @change="loadTargets"
          class="border rounded p-2">
          <option v-for="y in years" :key="y" :value="y">
            {{ y }}
          </option>
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
          <p>💰 Omset: <b>{{ formatRp(s.target?.target_omset) }}</b></p>
          <p>👣 Visit: <b>{{ s.target?.target_visit ?? 0 }}</b></p>
          <p>📄 Penawaran: <b>{{ s.target?.target_penawaran ?? 0 }}</b></p>
          <p>👥 Customer Baru: <b>{{ s.target?.target_new_customer ?? 0 }}</b></p>
        </div>

        <button
            class="w-full mt-2 py-2 rounded bg-blue-600 text-white text-sm"
            @click="goToInput(s)"
            >
            Input / Edit Target
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import axios from 'axios'
import { useRouter } from 'vue-router'

/* =====================
   CONFIG
===================== */
const apiBaseUrl = import.meta.env.VITE_API_BASE_URL
const token  = localStorage.getItem("api_token")
const role   = Number(localStorage.getItem("role"))
const branch = localStorage.getItem("branch")

const router = useRouter()

/* =====================
   STATE
===================== */
const years  = [2024, 2025, 2026,2027]
const year   = ref(new Date().getFullYear())
const month  = ref(new Date().getMonth() + 1)

const months = [
  { value: 1,  label: 'Januari' },
  { value: 2,  label: 'Februari' },
  { value: 3,  label: 'Maret' },
  { value: 4,  label: 'April' },
  { value: 5,  label: 'Mei' },
  { value: 6,  label: 'Juni' },
  { value: 7,  label: 'Juli' },
  { value: 8,  label: 'Agustus' },
  { value: 9,  label: 'September' },
  { value: 10, label: 'Oktober' },
  { value: 11, label: 'November' },
  { value: 12, label: 'Desember' },
]

const sales = ref([])

/* =====================
   AXIOS
===================== */
const api = axios.create({
  baseURL: apiBaseUrl,
  headers: {
    Authorization: token ? `Bearer ${token}` : undefined,
    Accept: 'application/json',
  },
})

/* ==========================
   LOAD SALES + TARGET
========================== */
const loadTargets = async () => {
  try {
    let users = []

    // 🔐 filter sales sesuai role
    if ([1,2,3].includes(role)) {
      const res = await api.get('/api/allusers')
      users = res.data.data.filter(u => u.role_id === 7)
    } else {
      const res = await api.get(`/api/usersbranch/${branch}`)
      users = res.data.data.filter(u => u.role_id === 7)
    }

    // ambil target tiap sales sesuai BULAN + TAHUN
    for (const u of users) {
      try {
        const t = await api.get(
          `/api/sales-targets/${u.id}/${month.value}/${year.value}`
        )
        u.target = t.data.data ?? null
      } catch {
        u.target = null
      }
    }

    sales.value = users
  } catch (e) {
    console.error('Gagal load target', e)
  }
}

/* =====================
   NAVIGATION
===================== */
const goToInput = (s) => {
  router.push({
    name: 'kpi target',
    params: { userId: s.id },
    query: {
      month: month.value,
      year: year.value,
    }
  })
}


/* =====================
   HELPER
===================== */
const formatRp = (v) => {
  if (!v) return 'Rp 0'
  return new Intl.NumberFormat('id-ID',{
    style:'currency',currency:'IDR',minimumFractionDigits:0
  }).format(v)
}

onMounted(loadTargets)
</script>
