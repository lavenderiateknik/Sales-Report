<template>
  <div class="flex flex-col gap-1">
    <label class="text-sm font-semibold text-gray-600">
      {{ label }}
    </label>

    <div class="relative">
      <!-- ICON -->
      <span
        v-if="icon"
        class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"
      >
        <component :is="icon" class="w-4 h-4" />
      </span>

      <input
        :type="type"
        :value="modelValue"
        @input="onInput"
        class="w-full border rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-400 outline-none"
        :class="{ 'pl-9': icon }"
        :placeholder="placeholder"
      />
    </div>

    <p v-if="hint" class="text-xs text-gray-400">
      {{ hint }}
    </p>
  </div>
</template>

<script setup>
const props = defineProps({
  modelValue: [String, Number],
  label: String,
  placeholder: String,
  hint: String,
  type: { type: String, default: 'number' },
  icon: [Object, Function],
})

const emit = defineEmits(['update:modelValue'])

const onInput = (e) => {
  const value =
    props.type === 'number'
      ? Number(e.target.value)
      : e.target.value

  emit('update:modelValue', value)
}
</script>

