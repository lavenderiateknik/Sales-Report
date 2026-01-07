<template>
  <div class="fixed inset-0 bg-black/40 flex items-center justify-center">
    <div class="bg-white rounded-2xl p-6 w-full max-w-lg space-y-4">
      <h2 class="text-xl font-bold">
        🎯 Target KPI — {{ sales.name }} ({{ year }})
      </h2>

      <div class="grid grid-cols-2 gap-3">
        <input v-model.number="form.target_omset"
          type="number" placeholder="Target Omset"
          class="border p-2 rounded" />

        <input v-model.number="form.target_visit"
          type="number" placeholder="Target Visit"
          class="border p-2 rounded" />

        <input v-model.number="form.target_penawaran"
          type="number" placeholder="Target Penawaran"
          class="border p-2 rounded" />

        <input v-model.number="form.target_new_customer"
          type="number" placeholder="Target Customer Baru"
          class="border p-2 rounded" />
      </div>

      <div class="flex justify-end gap-2 pt-3">
        <button class="px-4 py-2 rounded bg-gray-200"
          @click="$emit('close')">
          Batal
        </button>

        <button class="px-4 py-2 rounded bg-blue-600 text-white"
          @click="save">
          Simpan
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import axios from 'axios'

const props = defineProps({
  sales: Object,
  year: Number,
})
const emit = defineEmits(['close','saved'])

const apiBaseUrl = import.meta.env.VITE_API_BASE_URL
const token = localStorage.getItem("api_token")

const api = axios.create({
  baseURL: apiBaseUrl,
  headers: {
    Authorization: token ? `Bearer ${token}` : undefined,
    Accept: 'application/json',
  },
})

const form = ref({
  user_id: '',
  month: 1,
  year: props.year,
  target_omset: 0,
  target_visit: 0,
  target_penawaran: 0,
  target_new_customer: 0,
})

onMounted(async () => {
  form.value.user_id = props.sales.id

  // 🔄 ambil target existing
  try {
    const res = await api.get(
      `/api/sales-targets/${props.sales.id}/1/${props.year}`
    )
    if (res.data) Object.assign(form.value, res.data)
  } catch {}
})

const save = async () => {
  try {
    await api.post('/api/sales-targets', form.value)
    emit('saved')
    emit('close')
  } catch (e) {
    console.error(e)
    alert('Gagal simpan target')
  }
}
</script>
