<template>
  <div class="flex flex-col bg-[#10375C]/10 mx-2 my-2 rounded-2xl">

    <!-- HEADER -->
    <div class="flex flex-row items-center px-4 pt-3 pb-4 text-3xl text-slate-600">
      <span>Assigment BCI</span>
      <strong class="ml-2 uppercase">Data Progress</strong>
    </div>

    <!-- CONTENT -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 px-2 pb-4">

      <!-- LEFT PANEL -->
      <div>
        <!-- Branch Filter -->
        <select
          v-model="selectedBranch"
          class="bg-white w-40 p-2 m-2 rounded-md"
        >
          <option value="">All Branch</option>
          <option
            v-for="branch in branchList"
            :key="branch.id"
            :value="branch.id"
          >
            {{ branch.name }}
          </option>
        </select>

        <!-- SUMMARY -->
        <div class="px-4 py-2 text-sm text-slate-700 space-y-1">
          <div>
            Total BCI Data:
            <strong>{{ totalBCI }}</strong>
          </div>
          <div>
            Total Assigned:
            <strong>{{ totalAssigned }}</strong>
          </div>
          <div>
            Total Unassigned:
            <strong>{{ totalUnassigned }}</strong>
          </div>
        </div>
      </div>

      <!-- RIGHT PANEL -->
      <div class="md:col-span-3 flex justify-center items-center">
        <DoughnutChart
            :key="`${selectedBranch}-${totalBCI}`"
            :data="pieChartData"
            title="BCI Assignment Progress"
            class="w-96"
          />
      </div>

    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, computed, watch } from "vue";
import axios from "axios";
import PieChart from "./PieChart.vue";
import DoughnutChart from "./DoughnutChart.vue";

/* =========================
   CONFIG
========================= */
const apiBaseUrl = import.meta.env.VITE_API_BASE_URL;
const token = localStorage.getItem("api_token");

/* =========================
   STATE
========================= */
const branchList = ref([]);
const rawBCIData = ref([]);
const selectedBranch = ref("");

/* =========================
   FETCH BRANCHES
========================= */
const fetchBranch = async () => {
  try {
    const res = await axios.get(
      `${apiBaseUrl}/api/allbranches`,
      { headers: { Authorization: `Bearer ${token}` } }
    );
    branchList.value = res.data.data || [];
    console.log(branchList.va)
  } catch (error) {
    console.error("Fetch branch error:", error);
  }
};

/* =========================
   FETCH BCI DATA
========================= */
const fetchBCIData = async () => {
  try {
    const res = await axios.get(
      `${apiBaseUrl}/api/allcustomerdatabase`,
      { headers: { Authorization: `Bearer ${token}` } }
    );

    rawBCIData.value = res.data.data || [];
  } catch (error) {
    console.error("Fetch BCI error:", error);
  }
};

/* =========================
   FILTERED DATA (BY BRANCH)
========================= */
const filteredBCIData = computed(() => {
  if (!selectedBranch.value) return rawBCIData.value;

  return rawBCIData.value.filter(
    item => item.id_branch == selectedBranch.value
  );
});

/* =========================
   SUMMARY COUNTS
========================= */
const totalBCI = computed(() => filteredBCIData.value.length);

const totalAssigned = computed(() =>
  filteredBCIData.value.filter(
    item => item.assigned_to_user !== null
  ).length
);

const totalUnassigned = computed(
  () => totalBCI.value - totalAssigned.value
);

/* =========================
   PIE CHART DATA
========================= */
const pieChartData = computed(() => [
  { label: "Assigned", value: totalAssigned.value },
  { label: "Unassigned", value: totalUnassigned.value }
]);

/* =========================
   LIFECYCLE
========================= */
onMounted(() => {
  fetchBranch();
  fetchBCIData();
});
</script>

<style scoped>
/* optional */
</style>
