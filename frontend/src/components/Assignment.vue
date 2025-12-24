<template>
  <div class="max-w-350 mx-auto p-6">
    <div class="bg-white border border-amber-300 rounded-2xl shadow-xl shadow-slate-400 overflow-hidden">

      <div class="px-4 pt-4 pb-3 border-b">
        <div class="flex flex-col md:flex-row justify-between items-center gap-4">
          <h1 class="text-2xl font-semibold text-slate-800">
            Assignment <span class="text-amber-600 font-bold">Customer</span>
          </h1>

          <div class="flex items-center gap-3">
            <div v-if="loading" class="text-xs text-amber-600 animate-pulse font-medium">
              Memperbarui data...
            </div>
            <input
              v-model="search"
              placeholder="Cari customer, project, atau kota..."
              class="rounded-xl border border-slate-200 px-4 py-2 text-sm focus:ring-4 focus:ring-amber-100 outline-none transition-all w-72"
            />
          </div>
        </div>
      </div>

      <div class="overflow-x-auto">
        <table class="min-w-full text-sm table-fixed">
          <thead class="bg-slate-50 text-[11px] uppercase text-slate-500 font-bold">
            <tr>
              <th class="px-4 py-4 text-center w-16">No</th>
              <th class="px-4 py-4 text-left w-32">Project ID</th>
              <th class="px-4 py-4 text-left w-64">Company & PIC</th>
              <th class="px-4 py-4 text-left">Project Name</th>
              <th class="px-4 py-4 text-center w-32">Stage</th>
              <th class="px-4 py-4 text-center w-32">Town</th>
              <th class="px-4 py-4 text-right w-40">Nilai Project</th>
              <th class="px-4 py-4 text-center w-48 bg-amber-50/50">Assign To Sales</th>
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
                  {{ row.contact_first_name }} {{ row.contact_surname }}
                </div>
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
                {{ row.project_town }}
              </td>

              <td class="px-4 py-4 text-right font-bold text-slate-800">
                {{ formatCurrency(row.local_value) }}
              </td>

              <td class="px-4 py-4 text-center bg-amber-50/20">
                <div class="flex flex-col items-center gap-1 min-h-[50px] justify-center">
                  <select 
                    v-if="salesList.length > 0"
                    @change="handleAssign(row.id, $event.target.value)"
                    :disabled="loadingAssignment === row.id"
                    :class="[
                      'text-xs rounded-lg border px-2 py-1.5 outline-none transition-all w-full select-custom shadow-sm',
                      row.assigned_to_user 
                        ? 'border-emerald-200 bg-emerald-50 text-emerald-700 font-semibold' 
                        : 'border-slate-300 bg-white text-slate-500 focus:border-amber-400 focus:ring-2 focus:ring-amber-100'
                    ]"
                    :value="row.assigned_to_user || ''"
                  >
                    <option value="" disabled>-- Pilih Sales --</option>
                    <option v-for="sales in salesList" :key="sales.id" :value="sales.id">
                      {{ sales.name }}
                    </option>
                  </select>

                  <div v-else class="text-[10px] text-rose-500 font-medium italic">
                    Cabang tidak memiliki sales
                  </div>
                  
                  <span v-if="row.assigned_to_user" class="text-[9px] font-bold text-emerald-500 flex items-center gap-1">
                    <svg xmlns="http://www.w3.org/2000/svg" width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"/></svg>
                    TERTUGASKAN
                  </span>
                  <span v-else-if="salesList.length > 0" class="text-[9px] font-bold text-slate-400 italic">
                    BELUM DIPILIH
                  </span>
                </div>
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
    const salesUrl = role < 5 
      ? `${apiBaseUrl}/api/allusers` 
      : `${apiBaseUrl}/api/sales-by-branch`;

    const [resCustomers, resSales] = await Promise.all([
      axios.get(`${apiBaseUrl}/api/allcustomerdatabase`, {
        headers: { Authorization: `Bearer ${token}` }
      }),
      axios.get(salesUrl, {
        headers: { Authorization: `Bearer ${token}` }
      })
    ]);

    customers.value = resCustomers.data.data || resCustomers.data;
    // Pastikan data sales berupa array
    salesList.value = Array.isArray(resSales.data) ? resSales.data : (resSales.data.data || []);

  } catch (err) {
    console.error("Fetch Error:", err);
  } finally {
    loading.value = false;
  }
};

onMounted(fetchData);

/* ================= ASSIGNMENT LOGIC ================= */
const handleAssign = async (customerId, userId) => {
  loadingAssignment.value = customerId;
  try {
    await axios.post(`${apiBaseUrl}/api/customer/assign`, {
      customer_id: customerId,
      user_id: userId
    }, {
      headers: { Authorization: `Bearer ${token}` }
    });
    
    // Update local state secara manual agar tidak perlu refresh API full
    const index = customers.value.findIndex(c => c.id === customerId);
    if (index !== -1) {
      customers.value[index].assigned_to_user = userId;
      // Update object assigned_user jika ada di data row
      const sales = salesList.value.find(s => s.id == userId);
      if (sales) {
        customers.value[index].assigned_user = sales;
      }
    }
  } catch (err) {
    console.error(err);
    alert("Gagal memperbarui penugasan. Periksa koneksi atau role anda.");
  } finally {
    loadingAssignment.value = null;
  }
};

/* ================= FILTER & PAGINATION ================= */
const filteredData = computed(() => {
  if (!search.value) return customers.value;
  const s = search.value.toLowerCase();
  return customers.value.filter(item => 
    (item.project_name?.toLowerCase().includes(s)) ||
    (item.company_name?.toLowerCase().includes(s)) ||
    (item.project_id?.toLowerCase().includes(s)) ||
    (item.project_town?.toLowerCase().includes(s))
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

<style scoped>
.select-custom {
  appearance: none;
  background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%2394a3b8'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M19 9l-7 7-7-7'%3E%3C/path%3E%3C/svg%3E");
  background-repeat: no-repeat;
  background-position: right 0.5rem center;
  background-size: 1rem;
  padding-right: 2rem;
}
</style>