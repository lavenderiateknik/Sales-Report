<template>
  <div
    class="border-1 border-amber-500 m-3 p-3 rounded-xl bg-[#F4F6FF]/30 shadow-md/60 shadow-slate-400"
    :class="{ 'no-footer': !pageable }"
  >
    <div class="flex items-center justify-between mb-5">
      <h2 class="text-3xl">{{ title1 }} <strong>{{ title2 }}</strong></h2>
    </div>

    <vue3-datatable
      :rows="rowsData"
      :columns="cols"
      :loading="loading"
      :columnFilter="true"
      :pageable="pageable"
      :per-page="perPage"
      :skin="'bh-table-striped bh-table-hover'"
    >
      <template v-for="col in cols" :key="col.field" #[`cell-${col.field}`]="slotProps">
        <template v-if="col.field === 'actions'">
          <div v-html="slotProps.value"></div>
        </template>
        <template v-else>
          {{ slotProps.value }}
        </template>
      </template>
      </vue3-datatable>
  </div>
</template>

<script setup>
import Vue3Datatable from '@bhplugin/vue3-datatable';
import '@bhplugin/vue3-datatable/dist/style.css';

const props = defineProps({
  rowsData: { type: Array, required: true },
  cols: { type: Array, required: true },
  title1: { type: String, default: 'My Table' },
  title2: { type: String, default: '' },
  pageable: { type: Boolean, default: true },
  perPage: { type: Number, default: 10 },
  loading: { type: Boolean, default: false },
});
</script>

<style scoped>
.no-footer .bh-pagination {
  display: none !important;
}
</style>