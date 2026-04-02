<template>
    <div class="flex flex-col bg-[#10375C]/10 mx-2 my-2 rounded-2xl">
        <div class="flex flex-row items-center px-4 pt-3 pb-4 text-3xl text-slate-600">
            <span>Assigned</span>
            <strong class="ml-2 uppercase">Customer</strong> 
        </div>
        <div class="overflow-x-auto">
        <table class="min-w-full text-sm table-fixed">
          <thead class="bg-slate-50 text-[11px] uppercase text-slate-500 font-bold">
            <tr>
              <th class="px-4 py-4 text-center w-16">No</th>
              <th class="px-4 py-4 text-left w-32">Project ID</th>
              <th class="px-4 py-4 text-left w-64">Company & PIC</th>
              <th class="px-4 py-4 text-left w-64">Project Address</th>
              <th class="px-4 py-4 text-left">Project Name</th>
              <th class="px-4 py-4 text-center w-32">Stage</th>
              <th class="px-4 py-4 text-center w-32">Role On Project</th>
              <th class="px-4 py-4 text-center w-32">Town</th>
              <th class="px-4 py-4 text-right w-40">Nilai Project</th>
            </tr>
          </thead>

          <tbody class="divide-y divide-slate-100">
            <tr
              v-for="(row, index) in paginatedData"
              :key="row.id"
              class="hover:bg-slate-50/80 transition-colors"
            >
              <td class="px-4 py-4 text-center font-medium text-slate-400">
                {{ filteredData.length - ((page - 1) * pageSize) - index }}
              </td>

              <td class="px-4 py-4 text-slate-700 font-mono text-xs">
                {{ row.project_id || 'N/A' }}
              </td>

              <td class="px-4 py-4">
                <div class="font-bold text-slate-800 truncate whitespace-pre-wrap" :title="row.company_name">
                  {{ row.company_name }}
                </div>
                <div class="text-[10px] text-slate-500 flex items-center gap-1 mt-0.5">
                  <span class="bg-slate-100 px-1 rounded text-[9px] font-bold">PIC</span>
                  {{ row.contact_first_name }} {{ row.contact_surname }}<br/>
                </div>
                <div class="text-[10px] text-slate-500 flex items-center gap-1 mt-0.5">
                  <span class="bg-slate-100 px-1 rounded text-[9px] font-bold">Mobile</span>
                  {{ row.mobile?row.mobile: '-' }}<br/>
                </div>
                <div class="text-[10px] text-slate-500 flex items-center gap-1 mt-0.5">
                  <span class="bg-slate-100 px-1 rounded text-[9px] font-bold">Landline</span>
                  {{ row.contact_landline? row.contact_landline: '1' }}<br/>
                </div>
              </td>
              <td class="px-4 py-4 text-slate-600 leading-relaxed">
                {{ row.project_address }}
              </td>
              <td class="px-4 py-4 text-slate-600 leading-relaxed">
                {{ row.project_name }}
              </td>              

              <td class="px-4 py-4 text-center">
                <span class="px-2 py-1 rounded-md text-[10px] font-bold bg-slate-100 text-slate-600 border border-slate-200 uppercase">
                  {{ row.project_stage || '-' }}
                  
                </span>
              </td>

               <td class="px-4 py-4 text-center text-slate-600">
                {{ row.role_on_project || '-' }}
              </td>

              <td class="px-4 py-4 text-center text-slate-600">
                {{ row.project_town }}
              </td>

              <td class="px-4 py-4 text-right font-bold text-slate-800">
                {{ formatCurrency(row.local_value) }}
              </td>
            </tr>

            <tr v-if="!filteredData.length && !loading">
              <td colspan="8" class="text-center py-20">
                <div class="flex flex-col items-center opacity-40">
                  <svg xmlns="http://www.w3.org/2000/svg" class="w-12 h-12 mb-2" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
                  <p class="text-slate-500 italic">Data tidak ditemukan</p>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <div class="flex justify-between items-center px-6 py-4 border-t bg-slate-50">
        <div class="text-[11px] text-slate-500 font-bold uppercase tracking-wider">
          Total: {{ filteredData.length }} Customers
        </div>

        <div class="flex items-center gap-2">
          <button
            @click="page--"
            :disabled="page === 1"
            class="px-4 py-2 rounded-xl border bg-white text-xs font-bold shadow-sm disabled:opacity-30 hover:bg-slate-50 transition-all"
          >
            PREV
          </button>
          
          <div class="px-4 py-2 bg-white border rounded-xl text-xs font-bold text-slate-700 shadow-sm">
            {{ page }} / {{ totalPages }}
          </div>

          <button
            @click="page++"
            :disabled="page === totalPages"
            class="px-4 py-2 rounded-xl border bg-white text-xs font-bold shadow-sm disabled:opacity-30 hover:bg-slate-50 transition-all"
          >
            NEXT
          </button>
        </div>
      </div>
    </div>
</template>

<script setup>
    import { ref, computed, onMounted, watch } from "vue";
import axios from "axios";

/* ================= ENV & AUTH ================= */
const apiBaseUrl = import.meta.env.VITE_API_BASE_URL;
const token = localStorage.getItem("api_token");
// Konversi ke Number agar perbandingan role < 5 akurat
const role = Number(localStorage.getItem("role"));
const branch = localStorage.getItem("branch");

/* ================= STATE ================= */
const loading = ref(false);
const loadingAssignment = ref(null);
const customers = ref([]);
const salesList = ref([]);
const search = ref("");
const page = ref(1);
const pageSize = 10;

/* ================= FETCH DATA ================= */
const fetchData = async () => {
  loading.value = true;
  try {
    const salesUrl = role < 4 
      ? `${apiBaseUrl}/api/allusers` 
      : `${apiBaseUrl}/api/usersbranch/${branch}`;

    console.log(salesUrl);

    const [resCustomers, resSales] = await Promise.all([
      axios.get(`${apiBaseUrl}/api/customerdatabaseassigned`, {
        headers: { Authorization: `Bearer ${token}` }
      }),
      axios.get(salesUrl, {
        headers: { Authorization: `Bearer ${token}` }
      })
    ]);

    customers.value = resCustomers.data.data || resCustomers.data;
    console.log("customers.value:", customers.value);
    // Pastikan data sales berupa array
    salesList.value = Array.isArray(resSales.data) ? resSales.data : (resSales.data.data || []);

  } catch (err) {
    console.error("Fetch Error:", err);
  } finally {
    loading.value = false;
  }
};

onMounted(fetchData);


/* ================= FILTER & PAGINATION ================= */
const filteredData = computed(() => {
  if (!search.value) return customers.value;
  const s = search.value.toLowerCase();
  return customers.value.filter(item => 
    (item.project_name?.toLowerCase().includes(s)) ||
    (item.company_name?.toLowerCase().includes(s)) ||
    (item.project_id?.toLowerCase().includes(s)) ||
    (item.project_town?.toLowerCase().includes(s)) ||
    (item.role_on_project?.toLowerCase().includes(s))
  );
});

const totalPages = computed(() => Math.max(1, Math.ceil(filteredData.value.length / pageSize)));

const paginatedData = computed(() => {
  const start = (page.value - 1) * pageSize;
  return filteredData.value.slice(start, start + pageSize);
});

watch(search, () => (page.value = 1));

/* ================= FORMATTERS ================= */
const formatCurrency = (val) => {
  if (!val || isNaN(val)) return "-";
  return new Intl.NumberFormat("id-ID", {
    style: "currency",
    currency: "IDR",
    maximumFractionDigits: 0,
  }).format(val);
};

</script>

<style lang="scss" scoped>

</style>