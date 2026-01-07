<template>
  <div class="my-2 px-4 py-2 space-y-4 bg-[#10375C]/10 mx-1 rounded-2xl">
    <div class="p-6 bg-white rounded-2xl shadow space-y-6">

      <!-- HEADER INFO -->
      <div class="bg-blue-50 p-3 rounded-xl flex items-center gap-2">
        👤 <b>{{ salesName }}</b>
        <span class="text-sm text-gray-500">
          Target bulan {{ monthLabel }} {{ year }}
        </span>
      </div>

      <h2 class="text-xl font-bold flex items-center gap-2">
        🎯 Input Target KPI Sales
      </h2>

      <!-- KPI INPUT -->
      <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
        <KpiInput
          v-model="form.target_omset"
          label="Target Omset"
          placeholder="Contoh: 150000000"
          hint="Masukkan target omset bulanan"
          :icon="Banknote"
        />
        <KpiInput
          v-model="form.target_visit"
          label="Target Visit"
          placeholder="Contoh: 20"
          hint="Jumlah kunjungan ke customer"
          :icon="CalendarCheck"
        />
      </div>

      <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
        <KpiInput
          v-model="form.target_penawaran"
          label="Target Penawaran"
          placeholder="Contoh: 5"
          hint="Jumlah penawaran"
          :icon="UserPlus"
        />
        <KpiInput
          v-model="form.target_new_customer"
          label="Target Customer Baru"
          placeholder="Contoh: 3"
          hint="Customer baru bulan ini"
          :icon="FileText"
        />
      </div>

      <!-- PREVIEW -->
      <div class="bg-gray-50 p-4 rounded-xl">
        <h3 class="font-semibold mb-2">📊 Preview Target</h3>
        <ul class="text-sm space-y-1">
          <li>💰 Omset: <b>{{ formatRp(form.target_omset) }}</b></li>
          <li>👣 Visit: <b>{{ form.target_visit }}</b></li>
          <li>📄 Penawaran: <b>{{ form.target_penawaran }}</b></li>
          <li>👥 Customer Baru: <b>{{ form.target_new_customer }}</b></li>
        </ul>
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
          {{ loading ? 'Menyimpan...' : isEdit ? 'Update Target' : 'Simpan Target' }}
        </button>
      </div>

    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue'
import axios from 'axios'
import { useRoute, useRouter } from 'vue-router'
import KpiInput from './KpiInput.vue'
import { Banknote, CalendarCheck, UserPlus, FileText } from 'lucide-vue-next'

/* ======================
   ROUTE & CONFIG
====================== */
const route = useRoute()
const router = useRouter()

const apiBaseUrl = import.meta.env.VITE_API_BASE_URL
const token  = localStorage.getItem('api_token')

/* ======================
   PARAM FROM ROUTER
====================== */
const userId = ref(route.params.userId)
const month  = ref(Number(route.query.month) || new Date().getMonth() + 1)
const year   = ref(Number(route.query.year)  || new Date().getFullYear())

/* ======================
   STATE
====================== */
const loading = ref(false)
const isEdit  = ref(false)
const salesName = ref('')

const months = [
  { value: 1, label: 'Januari' }, { value: 2, label: 'Februari' },
  { value: 3, label: 'Maret' }, { value: 4, label: 'April' },
  { value: 5, label: 'Mei' }, { value: 6, label: 'Juni' },
  { value: 7, label: 'Juli' }, { value: 8, label: 'Agustus' },
  { value: 9, label: 'September' }, { value: 10, label: 'Oktober' },
  { value: 11, label: 'November' }, { value: 12, label: 'Desember' },
]

const form = ref({
  user_id: userId.value,
  month: month.value,
  year: year.value,
  target_omset: 0,
  target_visit: 0,
  target_penawaran: 0,
  target_new_customer: 0,
})

/* ======================
   AXIOS
====================== */
const api = axios.create({
  baseURL: apiBaseUrl,
  headers: {
    Authorization: token ? `Bearer ${token}` : undefined,
    Accept: 'application/json',
  },
})

/* ======================
   COMPUTED
====================== */
const monthLabel = computed(() => {
  const m = months.find(x => x.value === month.value)
  return m ? m.label : '-'
})

/* ======================
   LOAD DATA
====================== */
const loadSalesName = async () => {
  try {
    const res = await api.get(`/api/user/${userId.value}`)
    salesName.value = res.data.data?.name ?? 'Sales'
  } catch {
    salesName.value = 'Sales'
  }
}

const loadTarget = async () => {
  try {
    const res = await api.get(
      `/api/sales-targets/${userId.value}/${month.value}/${year.value}`
    )

    if (res.data.data) {
      Object.assign(form.value, res.data.data)
      isEdit.value = true
    }
  } catch {
    isEdit.value = false
  }
}

/* ======================
   SAVE
====================== */
const submit = async () => {
  loading.value = true
  try {
    await api.post('/api/sales-targets', form.value)
    alert(isEdit.value ? 'Target diperbarui' : 'Target disimpan')
    router.back()
  } catch (e) {
    console.error(e)
    alert('Gagal menyimpan target')
  } finally {
    loading.value = false
  }
}

/* ======================
   UTIL
====================== */
const formatRp = (value) => {
  if (!value) return 'Rp 0'
  return new Intl.NumberFormat('id-ID', {
    style: 'currency',
    currency: 'IDR',
    minimumFractionDigits: 0,
  }).format(value)
}

const resetForm = () => {
  form.value = {
    user_id: userId.value,
    month: month.value,
    year: year.value,
    target_omset: 0,
    target_visit: 0,
    target_penawaran: 0,
    target_new_customer: 0,
  }
}

/* ======================
   INIT
====================== */
onMounted(() => {
  loadSalesName()
  loadTarget()
})
</script>
