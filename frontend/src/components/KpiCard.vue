<template>
  <div class="border rounded-xl p-4 shadow-sm bg-white">
    <h3 class="font-semibold text-sm">{{ title }}</h3>

    <div class="mt-2 text-sm space-y-1">
      <p>Target: <b>{{ formatValue(target) }}</b></p>
      <p>Capaian: <b>{{ formatValue(actual) }}</b></p>

      <div class="mt-2">
        <div class="h-2 bg-gray-200 rounded">
          <div
            class="h-2 rounded"
            :style="{ width: percent + '%' }"
            :class="percent >= 100 ? 'bg-green-500' : 'bg-orange-500'"
          ></div>
        </div>
        <p class="text-xs mt-1">{{ percent }}%</p>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  title: String,
  target: Number,
  actual: Number,
  format: { type: String, default: null },
  suffix: { type: String, default: '' },
})

const percent = computed(() => {
  if (!props.target) return 0
  return Math.round((props.actual / props.target) * 100)
})

const formatValue = (val) => {
  if (props.format === 'currency') {
    return new Intl.NumberFormat('id-ID', {
      style: 'currency',
      currency: 'IDR',
    }).format(val || 0)
  }
  return `${val ?? 0}${props.suffix}`
}
</script>
