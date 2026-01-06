<template>
  <div class="min-h-screen bg-slate-50 p-6">
    <div class="flex flex-row items-center mb-8 text-2xl text-slate-700">
      <span>Pilih</span>
      <strong class="ml-2 text-blue-900">Tipe Laporan</strong>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 max-w-5xl mx-auto">
      
      <RouterLink to="/optionvisit" 
        class="group flex flex-col items-center justify-center bg-white p-8 rounded-3xl shadow-sm border border-slate-100 hover:shadow-xl hover:scale-105 transition-all duration-300">
        <div class="w-16 h-16 bg-green-100 text-green-600 rounded-2xl flex items-center justify-center mb-4 group-hover:bg-green-600 group-hover:text-white transition-colors">
          <Footprints/>
        </div>
        <span class="text-xl font-bold text-slate-800">Visit</span>
        <p class="text-sm text-slate-500 text-center mt-2">Laporan kunjungan langsung ke lokasi</p>
      </RouterLink>

      <RouterLink to="/phonecall" 
        class="group flex flex-col items-center justify-center bg-white p-8 rounded-3xl shadow-sm border border-slate-100 hover:shadow-xl hover:scale-105 transition-all duration-300">
        <div class="w-16 h-16 bg-fuchsia-100 text-fuchsia-600 rounded-2xl flex items-center justify-center mb-4 group-hover:bg-fuchsia-600 group-hover:text-white transition-colors">
          <PhoneCall/>
        </div>
        <span class="text-xl font-bold text-slate-800">Phone Call</span>
        <p class="text-sm text-slate-500 text-center mt-2">Laporan komunikasi via telepon</p>
      </RouterLink>

      <RouterLink to="/followup" 
        class="group flex flex-col items-center justify-center bg-white p-8 rounded-3xl shadow-sm border border-slate-100 hover:shadow-xl hover:scale-105 transition-all duration-300">
        <div class="w-16 h-16 bg-yellow-100 text-yellow-600 rounded-2xl flex items-center justify-center mb-4 group-hover:bg-yellow-600 group-hover:text-white transition-colors">
          <ListTodo/>
        </div>
        <span class="text-xl font-bold text-slate-800">Follow Up</span>
        <p class="text-sm text-slate-500 text-center mt-2">Tindak lanjut dari laporan sebelumnya</p>
      </RouterLink>

    </div>
  </div>
</template>

<script setup>
import { Footprints, ListTodo, PhoneCall } from 'lucide-vue-next';

</script>

<style lang="scss" scoped>

</style>