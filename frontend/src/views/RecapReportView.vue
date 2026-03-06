<template>
  <div class="bg-[#10375C]/10 mx-2 my-2 rounded-2xl p-4">

    <!-- SEARCH -->
    <div class="flex items-center justify-end gap-2 mb-4">
      <span class="font-medium">Search</span>
      <input
        v-model="search"
        type="text"
        placeholder="Search data..."
        class="bg-white border rounded-lg px-3 py-2 w-full md:w-64 focus:outline-none focus:ring-2 focus:ring-blue-400"
      />
    </div>

    <!-- CARD -->
    <div class="bg-white rounded-xl shadow-sm overflow-hidden border border-orange-400">
      <div class="p-4 border-b border-gray-100">
        <h1 class="text-2xl font-normal">
          Daily <span class="font-bold">Report</span>
        </h1>
      </div>

      <!-- TABLE -->
      <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">

          <thead>
            <tr class="bg-gray-50 text-gray-700 uppercase text-xs">
              <th class="px-4 py-3 border-b text-center w-12">No</th>
              <th class="px-4 py-3 border-b text-center">Date</th>
              <th class="px-4 py-3 border-b">Customer Name</th>
              <th class="px-4 py-3 border-b">Project Name</th>
              <th class="px-4 py-3 border-b">Report By</th>
              <th class="px-4 py-3 border-b">Type Report</th>
              <th class="px-4 py-3 border-b text-center">Actions</th>
            </tr>
          </thead>

          <tbody class="text-sm divide-y divide-gray-100">

            <tr v-if="loading">
              <td colspan="7" class="text-center py-10 text-gray-500">
                Loading data...
              </td>
            </tr>

            <tr v-else-if="filteredCustomers.length === 0">
              <td colspan="7" class="text-center py-10 text-gray-500">
                No data found.
              </td>
            </tr>

            <tr
              v-for="(row, index) in filteredCustomers"
              :key="row.id"
              class="hover:bg-gray-50 transition-colors"
            >
              <td class="px-4 py-3 text-center">
                {{ (currentPage - 1) * perPage + index + 1 }}
              </td>

              <td class="px-4 py-3 text-center whitespace-nowrap">
                {{ formatDate(row.date) }}
              </td>

              <td class="px-4 py-3 font-medium">
                {{ row.customer_name || '-' }}
              </td>

              <td class="px-4 py-3">
                {{ row.project_name || '-' }}
              </td>

              <td class="px-4 py-3">
                {{ row.user?.name || '-' }}
              </td>

              <td class="px-4 py-3">
                <span class="px-2 py-1 bg-blue-50 text-blue-600 rounded-md text-xs font-semibold">
                  {{ row.type_report?.name || '-' }}
                </span>
              </td>

              <td class="px-4 py-3 text-center">
                <button
                  @click="openDetail(row)"
                  class="px-4 py-1.5 bg-blue-500 text-white rounded-lg hover:bg-blue-600 shadow-sm transition-all active:scale-95"
                >
                  Detail
                </button>
              </td>
            </tr>

          </tbody>
        </table>
      </div>

      <!-- PAGINATION -->
      <div class="flex flex-col md:flex-row md:justify-between md:items-center gap-3 px-4 py-3 border-t bg-gray-50 text-sm">

        <!-- <div class="flex items-center gap-2">
          <span>Rows per page:</span>
          <select v-model="perPage" class="border rounded px-2 py-1">
            <option :value="10">10</option>
            <option :value="20">20</option>
            <option :value="50">50</option>
          </select>
        </div> -->

        <div>
          {{ (currentPage - 1) * perPage + 1 }} -
          {{ Math.min(currentPage * perPage, totalData) }}
          of {{ totalData }}
        </div>

        <div class="flex gap-2">
          <button
            class="px-3 py-1 border rounded disabled:opacity-40"
            :disabled="currentPage === 1"
            @click="currentPage--"
          >
            Prev
          </button>

          <button
            class="px-3 py-1 border rounded disabled:opacity-40"
            :disabled="currentPage === totalPages"
            @click="currentPage++"
          >
            Next
          </button>
        </div>

      </div>
    </div>

    <!-- MODAL -->
    <div v-if="showModal" class="fixed inset-0 bg-black/60 flex justify-center items-center z-50 p-4 backdrop-blur-sm">
      <div class="bg-white rounded-2xl p-6 w-full max-w-3xl relative overflow-y-auto max-h-[90vh] shadow-2xl">
        <h2 class="text-xl font-bold mb-4 border-b pb-3 text-[#10375C]">
          Report Detail {{ selectedCustomer?.type_report?.name }}
        </h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-y-3 gap-x-6 text-sm">
          <div class="flex flex-col border-b border-gray-50 pb-1">
            <span class="text-gray-400 text-xs uppercase font-bold">Date</span>
            <span class="font-medium">{{ formatDate(selectedCustomer?.date) }}</span>
          </div>
          <div class="flex flex-col border-b border-gray-50 pb-1">
            <span class="text-gray-400 text-xs uppercase font-bold">Customer Name</span>
            <span class="font-medium">{{ selectedCustomer?.customer_name }}</span>
          </div>
          <div class="flex flex-col border-b border-gray-50 pb-1">
            <span class="text-gray-400 text-xs uppercase font-bold">Check In</span>
            <span class="font-medium">{{ selectedCustomer?.check_in || '-' }}</span>
          </div>
          <div class="flex flex-col border-b border-gray-50 pb-1">
            <span class="text-gray-400 text-xs uppercase font-bold">Check Out</span>
            <span class="font-medium">{{ selectedCustomer?.check_out || '-' }}</span>
          </div>
          <div class="flex flex-col border-b border-gray-50 pb-1">
            <span class="text-gray-400 text-xs uppercase font-bold">Coordinate Check In</span>
            <span class="font-medium">{{ selectedCustomer?.coordinate_check_in || '-' }}</span>
          </div>
          <div class="flex flex-col border-b border-gray-50 pb-1">
            <span class="text-gray-400 text-xs uppercase font-bold">Coordinate Check Out</span>
            <span class="font-medium">{{ selectedCustomer?.coordinate_check_out || '-' }}</span>
          </div>
          <div class="flex flex-col border-b border-gray-50 pb-1">
            <span class="text-gray-400 text-xs uppercase font-bold">Project Name</span>
            <span class="font-medium">{{ selectedCustomer?.project_name }}</span>
          </div>
          <div class="flex flex-col border-b border-gray-50 pb-1">
            <span class="text-gray-400 text-xs uppercase font-bold">Type Project</span>
            <span class="font-medium">{{ selectedCustomer?.type_project }}</span>
          </div>
          <div class="flex flex-col border-b border-gray-50 pb-1">
            <span class="text-gray-400 text-xs uppercase font-bold">Reported By</span>
            <span class="font-medium">{{ selectedCustomer?.user?.name || '-' }}</span>
          </div>
          <div class="flex flex-col border-b border-gray-50 pb-1">
            <span class="text-gray-400 text-xs uppercase font-bold">PIC Name</span>
            <span class="font-medium">{{ selectedCustomer?.pic_name || '-' }}</span>
          </div>
          <div class="flex flex-col border-b border-gray-50 pb-1">
            <span class="text-gray-400 text-xs uppercase font-bold">PIC Phone</span>
            <span class="font-medium">{{ selectedCustomer?.pic_phone || '-' }}</span>
          </div>
          <div class="flex flex-col border-b border-gray-50 pb-1">
            <span class="text-gray-400 text-xs uppercase font-bold">PIC Position</span>
            <span class="font-medium">{{ selectedCustomer?.pic_position || '-' }}</span>
          </div>
          <div class="col-span-full bg-blue-50 p-3 rounded-lg mt-2">
            <span class="text-blue-400 text-xs uppercase font-bold block mb-1">Report Notes</span>
            <div v-if="!editNotes">
              <p class="text-gray-700 leading-relaxed  break-all">
                {{ selectedCustomer?.report_notes || 'No notes available.' }}
              </p>
                <button
                  @click="editNotes = true"
                  class="mt-2 text-sm bg-blue-500 text-white px-3 py-1 rounded"
                >
                  Edit Notes
                </button>
              </div>
              <div v-else>
                <textarea
                  v-model="notesValue"
                  class="w-full border rounded-lg p-2"
                  rows="10"
                ></textarea>

                  <div class="flex justify-between text-xs mt-1">
                    <span class="text-gray-500">
                      {{ notesLength }} / {{ minCharacters }} characters
                    </span>
                  </div>

                <div class="flex gap-2 mt-2">
                  <button
                    @click="updateNotes"
                    class="bg-green-500 text-white px-3 py-1 rounded"
                  >
                    Save
                  </button>

                  <button
                    @click="editNotes = false"
                    class="bg-gray-400 text-white px-3 py-1 rounded"
                  >
                    Cancel
                  </button>
                </div>
              </div>
          </div>
          <div v-if="selectedCustomer?.equipment_needs" class="col-span-full bg-blue-50 p-3 rounded-lg mt-2">
            <span class="text-blue-400 text-xs uppercase font-bold block mb-1">Equipment Needs</span>
            <p class="text-gray-700 leading-relaxed">{{ selectedCustomer?.equipment_needs || '-' }}</p>
          </div>
          <div v-if="selectedCustomer?.items_purchase_order" class="col-span-full bg-blue-50 p-3 rounded-lg mt-2">
            <span class="text-blue-400 text-xs uppercase font-bold block mb-1">Items Purchased</span>
            <p class="text-gray-700 leading-relaxed">{{ selectedCustomer?.items_purchase_order || '-' }}</p>
          </div>
          <div v-if="selectedCustomer?.nominal_purchase_order" class="col-span-full bg-blue-50 p-3 rounded-lg mt-2">
            <span class="text-blue-400 text-xs uppercase font-bold block mb-1">Nominal Purchase</span>
            <p class="text-gray-700 leading-relaxed">{{ formatCurrency(selectedCustomer?.nominal_purchase_order) || '-' }}</p>
          </div>
        </div>
        <button @click="closeModal" class="absolute top-4 right-4 text-gray-400 hover:text-red-500 text-3xl">
          &times;
        </button>

        <div v-if="selectedCustomer?.picture" class="col-span-full mt-3">
          <span class="text-gray-400 text-xs uppercase font-bold block mb-1">
            Photo
          </span>

          <img
            :src="`data:image/jpeg;base64,${selectedCustomer.picture}`"
            class="w-full max-h-80 object-contain rounded-lg border"
          />
        </div>

      </div>
    </div>

  </div>
</template>

<script setup>
import { ref, onMounted, watch, computed } from 'vue'
import axios from 'axios'
import currency from 'currency.js'

const formatDate = (val) => {
  if (!val) return '-'
  const d = new Date(val)
  return isNaN(d.getTime())
    ? val
    : d.toLocaleDateString('id-ID', {
        day: '2-digit',
        month: 'long',
        year: 'numeric'
      })
}

const formatCurrency = (val) => {
  return currency(val || 0, {
    symbol: 'Rp ',
    separator: '.',
    decimal: ',',
    precision: 0
  }).format()
}

const allCustomers = ref([])
const search = ref('')
const loading = ref(false)
const selectedCustomer = ref(null)
const showModal = ref(false)
const editNotes = ref(false)
const notesValue = ref('')

const currentPage = ref(1)
const perPage = ref(10)
const totalPages = ref(1)
const totalData = ref(0)

const token = localStorage.getItem('api_token')
const id = localStorage.getItem('id')
const branch = localStorage.getItem('branch')
const role = parseInt(localStorage.getItem('role'))
const apiBaseUrl = import.meta.env.VITE_API_BASE_URL

const notesLength = computed(() => notesValue.value?.length || 0)
const minCharacters = 200
const isNotesValid = computed(() => notesLength.value >= minCharacters)

const fetchSalesReports = async () => {
  let url = `${apiBaseUrl}/api/allsalesreports`

  if (role === 7) url = `${apiBaseUrl}/api/salesreports/${id}`
  else if ([6,5,4].includes(role))
    url = `${apiBaseUrl}/api/branchsalesreports/${branch}`

  loading.value = true
  try {
    const res = await axios.get(
      `${url}?page=${currentPage.value}&per_page=${perPage.value}&search=${search.value}`,
      {
        headers: { Authorization: `Bearer ${token}` }
      }
    )

    allCustomers.value = res.data.data
    
    totalPages.value = res.data.last_page
    totalData.value = res.data.total

  } catch (err) {
    console.error(err)
  } finally {
    loading.value = false
  }
}


const filteredCustomers = computed(() => {
  return allCustomers.value
})

watch([currentPage, perPage], () => {
  fetchSalesReports()
})

watch(search, () => {
  currentPage.value = 1
  fetchSalesReports()
})

const openDetail = (row) => {
  selectedCustomer.value = row
  notesValue.value = row.report_notes
  editNotes.value = false
  showModal.value = true
}

const closeModal = () => {
  showModal.value = false
}

const updateNotes = async () => {
  try {
    await axios.put(
      `${apiBaseUrl}/api/checkout-notes/${selectedCustomer.value.id}`,
      {
        report_notes: notesValue.value
      },
      {
        headers: { Authorization: `Bearer ${token}` }
      }
    )

    selectedCustomer.value.report_notes = notesValue.value
    editNotes.value = false
  } catch (err) {
    console.error(err)
  }
}


onMounted(fetchSalesReports)
</script>