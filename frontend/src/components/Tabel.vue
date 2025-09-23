<template>
  <div class="border-1 border-amber-500 m-3 p-3 rounded-xl bg-[#F4F6FF]/30 shadow-md/60 shadow-slate-400"
    :class="{ 'no-footer': !pageable }">
    <!-- Judul -->
    <div class="flex items-center justify-between mb-5">
      <h2 class="text-3xl">{{ title1 }} <strong>{{ title2 }}</strong></h2>
    </div>

    <!-- Datatable -->
    <vue3-datatable 
    :rows="rowsData" 
    :columns="cols" 
    :loading="loading" 
    :columnFilter="true" 
    :pageable="pageable"  
    :per-page="perPage" 
    :skin="'bh-table-striped bh-table-hover'"
     />
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

<style>
.no-footer .bh-pagination.bh-py-5 {
  display: none !important;
}

</style>
