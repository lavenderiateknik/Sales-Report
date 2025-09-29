<template>
  <div class="max-w-2xl mx-auto bg-white shadow-md rounded-lg p-6">
    <div class="flex flex-row items-center px-4  text-3xl text-slate-600">
      <UserRoundPlus class="pr-2 h-14 w-14" /> 
      <span>Add</span>
      <strong class="ml-2">User</strong>
    </div>

    <form @submit.prevent="submitForm" class="space-y-4">
      <!-- Name -->
      <div>
        <label class="block text-sm font-medium text-gray-700">Name</label>
        <input
          v-model="form.name"
          type="text"
          class="w-full border rounded-lg px-3 py-2 mt-1 focus:ring focus:ring-blue-200"
          placeholder="Enter full name"
          autocomplete="name"
          required
        />
      </div>

      <!-- Email -->
      <div>
        <label class="block text-sm font-medium text-gray-700">Email</label>
        <input
          v-model="form.email"
          type="email"
          class="w-full border rounded-lg px-3 py-2 mt-1 focus:ring focus:ring-blue-200"
          placeholder="Enter email"
          autocomplete="email"
          required
        />
      </div>

      <!-- Password -->
      <div>
        <label class="block text-sm font-medium text-gray-700">Password</label>
        <input
          v-model="form.password"
          autocomplete="new-name"
          type="password"
          class="w-full border rounded-lg px-3 py-2 mt-1 focus:ring focus:ring-blue-200"
          placeholder="Enter password"
          required
        />
      </div>

      <!-- Role -->
      <div>
        <label class="block text-sm font-medium text-gray-700">Role</label>
        <select
          v-model="form.role_id"
          class="w-full border rounded-lg px-3 py-2 mt-1 focus:ring focus:ring-blue-200"
          required
        >
          <option value="">-- Select Role --</option>
          <option v-for="role in roles" :key="role.id" :value="role.id" class="uppercase">
            {{ role.name }}
          </option>
        </select>
      </div>

      <!-- Branch -->
      <div>
        <label class="block text-sm font-medium text-gray-700">Branch</label>
        <select
          v-model="form.branch_id"
          class="w-full border rounded-lg px-3 py-2 mt-1 focus:ring focus:ring-blue-200"
          required
        >
          <option value="">-- Select Branch --</option>
          <option v-for="branch in branches" :key="branch.id" :value="branch.id">
            {{ branch.name }}
          </option>
        </select>
      </div>

      <!-- Submit Button -->
      <div class="flex justify-end">
        <button
          type="submit"
          class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg"
        >
          Save
        </button>
      </div>
    </form>
  </div>
</template>

<script setup>
import { ref, onMounted } from "vue";
import { UserRoundPlus } from "lucide-vue-next";
import axios from "axios";

const apiBaseUrl = import.meta.env.VITE_API_BASE_URL; // sesuaikan
const token = localStorage.getItem("api_token"); // kalau pakai token JWT

const form = ref({
  name: "",
  email: "",
  password: "",
  role_id: "",
  branch_id: "",
});

const roles = ref([]);
const branches = ref([]);

// fetch roles & branches dari API
onMounted(async () => {
  try {
    const [rolesRes, branchesRes] = await Promise.all([
      axios.get(`${apiBaseUrl}/api/allroles`, {
        headers: { Authorization: `Bearer ${token}` },
      }),
      axios.get(`${apiBaseUrl}/api/allbranches`, {
        headers: { Authorization: `Bearer ${token}` },
      }),
    ]);
    roles.value = rolesRes.data.data;
    branches.value = branchesRes.data.data;
  } catch (err) {
    console.error("Error fetching roles/branches:", err);
  }
});

// submit form
const submitForm = async () => {
  try {
    await axios.post(`${apiBaseUrl}/api/users`, form.value, {
      headers: { Authorization: `Bearer ${token}` },
    });
    alert("User berhasil ditambahkan!");
    // reset form
    form.value = { name: "", email: "", password: "", role_id: "", branch_id: "" };
  } catch (err) {
    console.error(err);
    alert("Gagal menambahkan user");
  }
};
</script>
