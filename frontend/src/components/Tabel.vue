<template>
  <div
    class="border border-amber-500 m-3 p-3 rounded-xl bg-[#F4F6FF]/30 shadow-md shadow-slate-400"
  >
    <!-- Header -->
    <div class="flex items-center justify-between mb-5">
      <h2 class="text-3xl">
        {{ title1 }} <strong>{{ title2 }}</strong>
      </h2>
    </div>

    <!-- Table -->
    <EasyDataTable
      :headers="computedHeaders"
      :items="rowsData"
      :loading="loading"
      :rows-per-page="perPage"
      :pagination="pageable"
      :hide-footer="!pageable"
      table-class-name="rounded-lg overflow-hidden"
    >
      <!-- === Slot Dinamis (untuk custom tombol seperti History) === -->
      <template>
        <div class="p-2">
          <EasyDataTable
            :headers="computedHeaders"
            :items="rowsData"
            :rows-per-page="10"
            :hide-footer="false"
            border-cell
            alternating
          >
            <!-- Default cell content -->
            <template v-for="col in computedHeaders" #[`item-${col.value}`]="slotProps">
              <slot :name="`cell-${col.value}`" v-bind="slotProps">
                {{ getValue(slotProps.item, col.value) || '-' }}
              </slot>
            </template>
          </EasyDataTable>
        </div>
      </template>

    </EasyDataTable>
  </div>
</template>

<script setup>
import { computed } from "vue";
import EasyDataTable from "vue3-easy-data-table";
import "vue3-easy-data-table/dist/style.css";

const props = defineProps({
  rowsData: { type: Array, required: true },
  cols: { type: Array, required: true },
  title1: { type: String, default: "My Table" },
  title2: { type: String, default: "" },
  pageable: { type: Boolean, default: true },
  perPage: { type: Number, default: 10 },
  loading: { type: Boolean, default: false },
});

const computedHeaders = computed(() =>
  props.cols.map((col) => ({
    text: col.title || col.text || "—",
    value: col.field || col.value || "",
    sortable: col.sortable ?? false,
  }))
);

// 🧠 Fungsi helper untuk baca nested key seperti "type_report.name"
function getValue(obj, path) {
  if (!obj || !path) return "-";
  try {
    const result = path.split(".").reduce((o, key) => o?.[key], obj);
    return result ?? "-";
  } catch {
    return "-";
  }
}
</script>
