<template>
  <div class="bg-[#10375C]/10 rounded-2xl  mx-4 my-2 py-4 p-4">
    <!-- Header + Button -->
    <div class="flex justify-between items-center mb-4">
      
      <button
        @click="showModal = true"
        class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg"
      >
        + Add User
      </button>
    </div>

    <!-- Komponen Tabel yang sudah ada -->
    <Tabel :rows-data="users" :cols="colsDataUser" title1="All" title2="user" :pageable="true" :per-page="10" :loading="loading" />

    <!-- Modal Add User -->
    <div
      v-if="showModal"
      class="fixed inset-0 bg-black/50 flex items-center justify-center z-50"
    >
      <div class="bg-slate-300 rounded-2xl shadow-lg w-full max-w-lg p-6 relative">
        <!-- Tombol Close -->
        <button
          @click="showModal = false"
          class="absolute top-2 right-2 text-gray-500 hover:text-gray-700"
        >
          ✕
        </button>

        <!-- Form Add User -->
        <AddUserForm @saved="handleUserSaved" />
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from "vue";
import axios from "axios";
import AddUserForm from "@/components/AddUserForm.vue";
import Tabel from "@/components/Tabel.vue";

const apiBaseUrl = import.meta.env.VITE_API_BASE_URL;
const token = localStorage.getItem("api_token"); // sesuai info Anda

const users = ref([]);
const showModal = ref(false);

// fetch data user
const fetchUsers = async () => {
  try {
    const res = await axios.get(`${apiBaseUrl}/api/allusers`, {
      headers: { Authorization: `Bearer ${token}` },
    });

    // tambahkan nomor urut ke setiap row
    users.value = (res.data.data || []).map((item, index) => ({
      ...item,
      no: index + 1,
    }));
  } catch (err) {
    console.error("Error fetching users:", err);
  }
};

const colsDataUser = ref([
  { field: 'no', title: 'No', align: 'center' },
  { field: 'name', title: 'Name', align: 'center' },
  { field: 'email', title: 'Email', align: 'center' },
  { field: 'branch.name', title: 'Branch', align: 'center' },
  { field: 'role.name', title: 'Role', align: 'center' },
]);



// dipanggil setelah user disimpan
const handleUserSaved = () => {
  fetchUsers();
  showModal.value = false;
};

onMounted(fetchUsers);
</script>
