<template>
  <div class="p-6 space-y-6 bg-[#10375C]/10 mx-2 my-2 rounded-2xl">
    <h1 class="text-2xl font-bold">Dashboard KPI Sales</h1>

    <!-- Filter -->
    <div class="flex gap-3">
      <!-- USER -->
      <select v-model="userId" class="border rounded p-2">
        <option disabled value="">Pilih Sales</option>
        <option
          v-for="u in users"
          :key="u.id"
          :value="u.id"
        >
          {{ u.name }}
        </option>
      </select>

      <!-- MONTH -->
      <select v-model="month" class="border rounded p-2">
        <option
            v-for="m in months"
            :key="m.value"
            :value="m.value"
        >
            {{ m.label }}
        </option>
        </select>


      <!-- YEAR -->
      <select v-model="year" class="border rounded p-2">
        <option v-for="y in years" :key="y" :value="y">
          {{ y }}
        </option>
      </select>

      <button
        @click="loadKpi"
        class="bg-blue-600 text-white px-4 rounded"
      >
        Tampilkan
      </button>
    </div>

    <!-- KPI -->
    <div v-if="kpi" class="grid grid-cols-2 md:grid-cols-3 gap-4">
      <KpiCard
        title="Omset"
        :target="Number(kpi.target.omset)"
        :actual="kpi.actual.omset"
        format="currency"
      />

      <KpiCard
        title="Visit"
        :target="kpi.target.visit"
        :actual="kpi.actual.visit"
      />

      <KpiCard
  title="Penawaran"
  :target="kpi.target.penawaran"
  :actual="kpi.actual.offering"
/>
      <KpiCard
        title="Customer Baru"
        :target="kpi.target.new_customer"
        :actual="kpi.actual.new_customer"
      />

      <KpiCard
        title="Absensi"
        :target="100"
        :actual="kpi.attendance.rate ?? 0"
        suffix="%"
      />
      <!-- TOTAL KPI -->
<div class="col-span-full mt-6">
  <div class="border rounded-xl p-4 bg-white shadow">
    <h3 class="text-lg font-bold mb-2">Total KPI</h3>

    <div class="text-3xl font-bold text-blue-600">
      {{ totalKpiPercent }}%
    </div>

    <div class="w-full bg-gray-200 h-3 rounded mt-2 overflow-hidden">
      <div
        class="h-3 bg-blue-600 transition-all"
        :style="{ width: totalKpiPercent + '%' }"
      ></div>
    </div>

    <p class="text-sm text-gray-500 mt-1">
      Rata-rata capaian dari Omset, Visit, Penawaran, Customer Baru & Absensi
    </p>
  </div>
</div>

    </div>


    <p v-else class="text-gray-500 italic">
      Pilih sales, bulan & tahun untuk melihat KPI.
    </p>
  </div>
</template>

<script setup>
import { ref, onMounted,computed } from 'vue'
import axios from 'axios'
import KpiCard from '@/components/KpiCard.vue'

/* =========================
   CONFIG
========================= */
const apiBaseUrl = import.meta.env.VITE_API_BASE_URL
const token  = localStorage.getItem("api_token")
const role   = Number(localStorage.getItem("role"))
const branch = localStorage.getItem("branch")

/* =========================
   STATE
========================= */
const users  = ref([])
const userId = ref('')
const year   = ref(new Date().getFullYear())
const years  = [2024, 2025, 2026, 2027, 2028]
const kpi    = ref(null)
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
const month = ref(new Date().getMonth() + 1)


/* =========================
   AXIOS
========================= */
const api = axios.create({
  baseURL: apiBaseUrl,
  headers: {
    Authorization: token ? `Bearer ${token}` : undefined,
    Accept: 'application/json',
  },
})

/* =========================
   METHODS
========================= */
const fetchUsers = async () => {
  try {
    // role 1–4: lihat semua sales
    if ([1,2,3].includes(role)) {
      const res = await api.get('/api/allusers')
      users.value = res.data.data.filter(u => u.role_id == 7)
    }
    // role 5–7: hanya branch sendiri
    else {
      const res = await api.get(`/api/usersbranch/${branch}`)
      users.value = res.data.data.filter(u => u.role_id === 7)
    }

    // auto pilih user pertama
    if (users.value.length) {
      userId.value = users.value[0].id
    }
  } catch (e) {
    console.error('Gagal load users', e)
  }
}

const loadKpi = async () => {
  if (!userId.value) return alert('Pilih sales dulu')

  try {
    const res = await api.get(
      `/api/kpi/${userId.value}/${month.value}/${year.value}`
    )
    kpi.value = res.data
    
  } catch (e) {
    console.error('Gagal load KPI', e)
    alert('Gagal memuat KPI')
  }
} 

const totalKpiPercent = computed(() => {
  if (!kpi.value) return 0

  const items = [
    { actual: kpi.value.actual.omset,        target: Number(kpi.value.target.omset) },
    { actual: kpi.value.actual.visit,        target: kpi.value.target.visit },
    { actual: kpi.value.actual.offering,     target: kpi.value.target.penawaran },
    { actual: kpi.value.actual.new_customer, target: kpi.value.target.new_customer },
    { actual: kpi.value.attendance.rate,     target: 100 },
  ]

  let totalPercent = 0

  items.forEach(i => {
    let percent = 0

    if (i.target > 0) {
      percent = Math.min((i.actual / i.target) * 100, 100)
    }
    // kalau target 0 → percent tetap 0

    totalPercent += percent
  })

  // selalu dibagi 5 (jumlah KPI)
  return Math.round(totalPercent / items.length)
})



onMounted(() => {
  fetchUsers()
})
</script>
