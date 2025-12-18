<template>
    <div class="">
         <div class="flex flex-row items-center px-4 pt-3 pb-4 text-3xl text-slate-600">
            <span>Pilih</span>
            <strong class="ml-2"> Tipe Laporan</strong>
        </div>
        <div class="flex flex-col item-center bg-[#10375C]/10 mx-2 my-2 rounded-2xl px-3 md:px-64 py-16 gap-4 ">
            <RouterLink to="/optionvisit" class=" bg-green-600 mx-auto px-[30%] py-3 rounded-2xl text-white text-5xl cursor-pointer ">Visit</RouterLink>
            <RouterLink to="/phonecall" class=" bg-fuchsia-600 mx-auto px-[20%] py-3 rounded-2xl text-white text-5xl cursor-pointer ">Phone Call</RouterLink>
            <RouterLink to="/followup" class="bg-yellow-600 mx-auto px-[20%] py-3 rounded-2xl text-white text-5xl cursor-pointer ">Follow Up</RouterLink>
        </div>
    </div>
</template>

<script setup>

</script>

<style lang="scss" scoped>

</style>