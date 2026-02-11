<template>
  <form @submit.prevent="submitForm" class="space-y-4">
    <div v-if="loading" class="text-center py-4 text-blue-600">
      <p>Memuat data user...</p>
    </div>

    <div v-else-if="fetchError" class="text-center py-4 text-red-600">
      <p>Gagal memuat data user: {{ fetchError }}</p>
    </div>

    <div v-else class="px-2 py-2">
      <!-- Name -->
      <div class="flex flex-col">
        <label class="block py-2 text-sm font-medium text-gray-700">Name</label>
        <input
          v-model="form.name"
          type="text"
          class="w-full border rounded-lg px-3 py-2 mt-1 focus:ring focus:ring-blue-200"
          autocomplete="name"
          required
        />
        <p v-if="errors.name" class="text-red-500 text-sm mt-1">{{ errors.name[0] }}</p>
      </div>

      <!-- Email -->
      <div class="flex flex-col">
        <label class="block py-2 text-sm font-medium text-gray-700">Email</label>
        <input
          v-model="form.email"
          type="email"
          class="w-full border rounded-lg px-3 py-2 mt-1 focus:ring focus:ring-blue-200"
          autocomplete="email"
          required
        />
        <p v-if="errors.email" class="text-red-500 text-sm mt-1">{{ errors.email[0] }}</p>
      </div>

      <!-- Password -->
      <div class="flex flex-col">
        <label class="block py-2 text-sm font-medium text-gray-700">Password (Kosongkan jika tidak diubah)</label>
        <input
          v-model="form.password"
          autocomplete="new-password"
          type="password"
          class="w-full border rounded-lg px-3 py-2 mt-1 focus:ring focus:ring-blue-200"
          placeholder="Enter new password (optional)"
        />
        <p v-if="errors.password" class="text-red-500 text-sm mt-1">{{ errors.password[0] }}</p>
      </div>

      <!-- Confirm Password -->
      <div class="flex flex-col">
        <label class="block py-2 text-sm font-medium text-gray-700">Confirm New Password</label>
        <input
          v-model="form.password_confirmation"
          autocomplete="new-password"
          type="password"
          class="w-full border rounded-lg px-3 py-2 mt-1 focus:ring focus:ring-blue-200"
          placeholder="Confirm new password"
        />
      </div>

      <!-- Role -->
      <div v-if="role === 1" class="flex flex-col">
        <label class="block py-2 text-sm font-medium text-gray-700">Role</label>
        <select
          v-model="form.role_id"
          class="w-full border rounded-lg px-3 py-2 mt-1 focus:ring focus:ring-blue-200"
          required
        >
          <option value="">-- Select Role --</option>
          <option v-for="r in roles" :key="r.id" :value="r.id">{{ r.name }}</option>
        </select>
        <p v-if="errors.role_id" class="text-red-500 text-sm mt-1">{{ errors.role_id[0] }}</p>
      </div>

      <!-- Branch -->
      <div v-if="role === 1" class="flex flex-col">
        <label class="block py-2 text-sm font-medium text-gray-700">Branch</label>
        <select
          v-model="form.branch_id"
          class="w-full border rounded-lg px-3 py-2 mt-1 focus:ring focus:ring-blue-200"
          required
        >
          <option value="">-- Select Branch --</option>
          <option v-for="b in branches" :key="b.id" :value="b.id">{{ b.name }}</option>
        </select>
        <p v-if="errors.branch_id" class="text-red-500 text-sm mt-1">{{ errors.branch_id[0] }}</p>
      </div>

      <p v-if="errors.general" class="text-red-600 text-sm">{{ errors.general }}</p>

      <div class="flex justify-end pt-2">
        <button
          type="submit"
          class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg disabled:opacity-60"
          :disabled="submitting"
        >
          <span v-if="!submitting">Update User</span>
          <span v-else>Updating...</span>
        </button>
      </div>
    </div>
  </form>
</template>

<script setup>
import { ref, watch, onMounted } from 'vue';
import axios from 'axios';
import { useRouter } from "vue-router";

const router = useRouter();
const props = defineProps({
  userId: { type: Number, required: true },
  role: { type: Number, required: true }, // menerima role dari parent
});

const emit = defineEmits(['saved']);

const apiBaseUrl = import.meta.env.VITE_API_BASE_URL;
const token = localStorage.getItem('api_token');

const form = ref({
  name: '',
  email: '',
  password: '',
  password_confirmation: '',
  role_id: '',
  branch_id: '',
});

const errors = ref({});
const roles = ref([]);
const branches = ref([]);
const loading = ref(true);
const submitting = ref(false);
const fetchError = ref(null);

// --- Fetch roles & branches ---
const fetchReferenceData = async () => {
  try {
    const [rolesRes, branchesRes] = await Promise.all([
      axios.get(`${apiBaseUrl}/api/allroles`, { headers: { Authorization: `Bearer ${token}` } }),
      axios.get(`${apiBaseUrl}/api/allbranches`, { headers: { Authorization: `Bearer ${token}` } }),
    ]);
    roles.value = rolesRes.data.data || [];
    branches.value = branchesRes.data.data || [];
  } catch (err) {
    console.error('Failed to fetch reference data:', err);
    fetchError.value = 'Gagal memuat daftar Role atau Branch.';
  }
};

// --- Fetch user data ---
const fetchUserData = async (id) => {
  if (!id) return;
  loading.value = true;
  fetchError.value = null;

  try {
    const res = await axios.get(`${apiBaseUrl}/api/user/${id}`, {
      headers: { Authorization: `Bearer ${token}` },
    });
    const user = res.data.data;

    form.value.name = user.name || '';
    form.value.email = user.email || '';
    form.value.role_id = user.role_id || '';
    form.value.branch_id = user.branch_id || '';
    form.value.password = '';
    form.value.password_confirmation = '';
  } catch (err) {
    console.error('Failed to fetch user data:', err);
    fetchError.value = `Tidak dapat menemukan user ID ${id}.`;
  } finally {
    loading.value = false;
  }
};

// --- Watch userId perubahan ---
watch(() => props.userId, (newId) => {
  fetchUserData(newId);
}, { immediate: true });

// --- Submit form ---
const submitForm = async () => {
  submitting.value = true;
  errors.value = {};

  const dataToSend = {
    name: form.value.name,
    email: form.value.email,
    role_id: form.value.role_id,
    branch_id: form.value.branch_id,
  };
  if (form.value.password) {
    dataToSend.password = form.value.password;
    dataToSend.password_confirmation = form.value.password_confirmation;
  }

  try {
    await axios.put(`${apiBaseUrl}/api/updateuser/${props.userId}`, dataToSend, {
      headers: { Authorization: `Bearer ${token}` },
    });

    emit('saved'); // SPA-friendly, tidak reload halaman
    router.push('/');
  } catch (err) {
    if (err.response?.status === 422) {
      errors.value = err.response.data.errors;
      console.log('Validation errors:', errors.value);
    } else {
      console.error('Gagal update user:', err);
      errors.value.general = 'Gagal menyimpan perubahan. Coba lagi.';
    }
  } finally {
    submitting.value = false;
  }
};

onMounted(fetchReferenceData);
</script>
