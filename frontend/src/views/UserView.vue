<template>
  <div class="bg-[#10375C]/10 rounded-2xl mx-4 my-2 py-4 p-4">
    <div class="flex justify-between items-center mb-4">
      <button 
        @click="showModal = true" 
        class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg"
      >
        + Add User
      </button>
    </div>

    <div 
        class="border border-amber-500 m-3 p-3 rounded-xl bg-white shadow-xl overflow-x-auto"
    >
        <div class="flex items-center justify-between mb-5">
            <h2 class="text-3xl">User <strong>List</strong></h2>
        </div>

        <div v-if="loading" class="text-center py-8 text-lg text-gray-500">
            Loading data...
        </div>
        
        <table v-else class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th v-for="col in colsDataUser" :key="col.field" scope="col"
                        class="px-6 py-3 text-xs font-medium text-gray-500 uppercase tracking-wider"
                        :class="{'text-center': col.align === 'center'}"
                    >
                        {{ col.title }}
                    </th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <tr v-for="user in users" :key="user.id">
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">{{ user.no }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 text-center">{{ user.name }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">{{ user.email }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">{{ user.branch?.name || '-' }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">{{ user.role?.name || '-' }}</td>
                    
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-center">
                        <a 
                            href="#" 
                            class="text-blue-600 hover:text-blue-800 font-medium mr-4"
                            @click.prevent="editUser(user.id)" 
                        >
                            Edit
                        </a>
                        <button 
                            class="text-red-600 hover:text-red-800 font-medium" 
                            @click="deleteUser(user.id)"
                        >
                            Delete
                        </button>
                    </td>
                </tr>
                <tr v-if="!loading && users.length === 0">
                    <td :colspan="colsDataUser.length" class="px-6 py-4 text-center text-gray-500">
                        Tidak ada data pengguna.
                    </td>
                </tr>
            </tbody>
        </table>

    </div>
    <div v-if="showModal" class="fixed inset-0 bg-black/50 flex items-center justify-center z-50">
      <div class="bg-white m-4 p-6 rounded-2xl shadow-lg w-full max-w-lg relative">
        <button 
          @click="showModal = false" 
          class="absolute top-2 right-2 text-gray-500 hover:text-gray-700 text-xl"
        >
          ✕
        </button>
        <h3 class="text-xl font-bold mb-4">Tambah User Baru</h3>
        <AddUserForm @saved="handleUserSaved" />
      </div>
    </div>

    <div v-if="showEditModal" class="fixed inset-0 bg-black/50 flex items-center justify-center z-50">
      <div class="bg-white m-4 p-6 rounded-2xl shadow-lg w-full max-w-lg relative">
        <button 
          @click="showEditModal = false" 
          class="absolute top-2 right-2 text-gray-500 hover:text-gray-700 text-xl"
        >
          ✕
        </button>
        <h3 class="text-xl font-bold mb-4">Edit User ID: {{ editingUserId }}</h3>
        
        <EditUserForm 
          :user-id="editingUserId" 
          @saved="handleEditSaved"
        />
        
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from "vue"; 
import axios from "axios";
import AddUserForm from "@/components/AddUserForm.vue";
// 1. IMPORT KOMPONEN BARU
import EditUserForm from "@/components/EditUserForm.vue"; 

const apiBaseUrl = import.meta.env.VITE_API_BASE_URL;
const token = localStorage.getItem("api_token");

const users = ref([]);
const showModal = ref(false); 
const showEditModal = ref(false); 
const editingUserId = ref(null); 
const loading = ref(false);

// --- Fungsi Data ---

const fetchUsers = async () => {
  loading.value = true;
  try {
    const res = await axios.get(`${apiBaseUrl}/api/allusers`, {
      headers: { Authorization: `Bearer ${token}` },
    });

    users.value = (res.data.data || []).map((item, index) => ({
      ...item,
      no: index + 1,
    }));
    
  } catch (err) {
    console.error("Error fetching users:", err);
  } finally {
    loading.value = false;
  }
};

// --- Konfigurasi Kolom ---

const colsDataUser = ref([
  { field: "no", title: "No", align: "center" },
  { field: "name", title: "Name", align: "center" },
  { field: "email", title: "Email", align: "center" },
  { field: "branch.name", title: "Branch", align: "center" },
  { field: "role.name", title: "Role", align: "center" },
  { field: "actions", title: "Action", align: "center" } 
]);

// --- Fungsi Aksi ---

const editUser = (id) => {
    // 1. Simpan ID user yang akan diedit
    editingUserId.value = id; 
    
    // 2. Tampilkan Modal Edit
    showEditModal.value = true;
};

const deleteUser = async (id) => {
    if (!confirm("Yakin ingin menghapus user ini?")) return;
    try {
        await axios.delete(`${apiBaseUrl}/api/deleteuser/${id}`, {
            headers: { Authorization: `Bearer ${token}` },
        });
        alert("User berhasil dihapus");
        fetchUsers(); 
    } catch (err) {
        console.error("Gagal hapus user:", err);
        alert("Gagal menghapus user");
    }
};

const handleUserSaved = () => {
    fetchUsers();
    showModal.value = false;
};

// 3. TAMBAHKAN HANDLER UNTUK MODAL EDIT
const handleEditSaved = () => {
    fetchUsers(); // Refresh data tabel setelah edit sukses
    showEditModal.value = false; // Tutup modal edit
};

onMounted(fetchUsers);
</script>