<template>
  <div class="max-w-2xl mx-auto bg-white shadow-md rounded-lg p-6">
    <div class="flex flex-row items-center px-4 text-3xl text-slate-600">
      <UserRoundPlus class="pr-2 h-14 w-14" />
      <span>Add</span>
      <strong class="ml-2">User</strong>
    </div>

    <form @submit.prevent="submitForm" class="space-y-1">
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
        <p v-if="errors.name" class="text-red-500 text-sm mt-1">{{ errors.name[0] }}</p>
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
        <p v-if="errors.email" class="text-red-500 text-sm mt-1">{{ errors.email[0] }}</p>
      </div>

      <!-- Password -->
      <div>
        <label class="block text-sm font-medium text-gray-700">Password</label>
        <input
          v-model="form.password"
          autocomplete="new-password"
          type="password"
          class="w-full border rounded-lg px-3 py-2 mt-1 focus:ring focus:ring-blue-200"
          placeholder="Enter password"
          required
        />
        <p v-if="errors.password" class="text-red-500 text-sm mt-1">{{ errors.password[0] }}</p>
      </div>

      <!-- Password confirmation -->
      <div>
        <label class="block text-sm font-medium text-gray-700">Confirm Password</label>
        <input
          v-model="form.password_confirmation"
          autocomplete="new-password"
          type="password"
          class="w-full border rounded-lg px-3 py-2 mt-1 focus:ring focus:ring-blue-200"
          placeholder="Confirm password"
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
          <option v-for="role in roles" :key="role.id" :value="role.id">{{ role.name }}</option>
        </select>
        <p v-if="errors.role_id" class="text-red-500 text-sm mt-1">{{ errors.role_id[0] }}</p>
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
          <option v-for="branch in branches" :key="branch.id" :value="branch.id">{{ branch.name }}</option>
        </select>
        <p v-if="errors.branch_id" class="text-red-500 text-sm mt-1">{{ errors.branch_id[0] }}</p>
      </div>

      <!-- General error -->
      <p v-if="errors.general" class="text-red-600 text-sm">{{ errors.general }}</p>

      <!-- Submit Button -->
      <div class="flex justify-end">
        <button
          type="submit"
          class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg disabled:opacity-60"
          :disabled="submitting"
        >
          <span v-if="!submitting">Save</span>
          <span v-else>Saving...</span>
        </button>
      </div>
    </form>
  </div>
</template>

<script setup>
import { ref, onMounted } from "vue";
import { UserRoundPlus } from "lucide-vue-next";
import axios from "axios";

const apiBaseUrl = import.meta.env.VITE_API_BASE_URL || "http://192.168.88.22:8000";
const token = localStorage.getItem("api_token");

const form = ref({
  name: "",
  email: "",
  password: "",
  password_confirmation: "",
  role_id: "",
  branch_id: "",
});

const roles = ref([]);
const branches = ref([]);
const errors = ref({});
const submitting = ref(false);

// fetch roles & branches
onMounted(async () => {
  try {
    const [rolesRes, branchesRes] = await Promise.all([
      axios.get(`${apiBaseUrl}/api/allroles`, { headers: { Authorization: `Bearer ${token}`, Accept: "application/json" } }),
      axios.get(`${apiBaseUrl}/api/allbranches`, { headers: { Authorization: `Bearer ${token}`, Accept: "application/json" } }),
    ]);
    roles.value = rolesRes.data.data ?? rolesRes.data;
    branches.value = branchesRes.data.data ?? branchesRes.data;
  } catch (err) {
    console.error("Error fetching roles/branches:", err.response?.data ?? err);
  }
});

const submitForm = async () => {
  errors.value = {};
  submitting.value = true;

  try {
    // pastikan mengirim angka (opsional)
    const payload = {
      name: form.value.name,
      email: form.value.email,
      password: form.value.password,
      password_confirmation: form.value.password_confirmation,
      role_id: form.value.role_id ? Number(form.value.role_id) : null,
      branch_id: form.value.branch_id ? Number(form.value.branch_id) : null,
    };

    const res = await axios.post(`${apiBaseUrl}/api/createuser`, payload, {
      headers: {
        Authorization: token ? `Bearer ${token}` : undefined,
        Accept: "application/json",
      },
    });

    alert(res.data?.message ?? "User berhasil ditambahkan!");
    form.value = { name: "", email: "", password: "", password_confirmation: "", role_id: "", branch_id: "" };
    window.location.href = "/adduser";
  } catch (err) {
    // tampilkan full response untuk debugging
    console.log("create user error response:", err.response?.data ?? err);

    if (err.response) {
      if (err.response.status === 422) {
        // Laravel validation errors biasanya di { errors: { field: [..] } }
        errors.value = err.response.data.errors ?? { general: err.response.data.message ?? "Validation failed" };
      } else {
        errors.value = { general: err.response.data?.message ?? "Gagal menambahkan user" };
      }
    } else {
      errors.value = { general: "Tidak dapat terhubung ke server" };
    }
  } finally {
    submitting.value = false;
  }
};
</script>
