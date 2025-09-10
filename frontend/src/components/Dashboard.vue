<template>
    <div class="bg-slate-100 mx-4 my-4 p-2 rounded-xl shadow-xl/40 shadow-slate-700 border border-[#EB8317]">
        <h1 class=" text-2xl text-slate-600 text-left mb-2">Sales <strong>Dasboard </strong></h1>
        <div class=" flex flex-col md:flex-row gap-3">
            <div class="w-full md:w-1/2 ">
                <Chart class="px-auto" />
            </div>
            <div class="w-full md:w-1/2 ">
                <Bar class=" " />
            </div>
        </div>
    </div>
</template>

<script setup>
import Chart from './Chart.vue';
import Bar from './Bar.vue';




</script>

<style lang="scss" scoped>

</style>