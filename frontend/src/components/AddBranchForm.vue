<template>
  <div>
    <h3 class="text-xl font-bold mb-4">Tambah Cabang</h3>
    <form @submit.prevent="saveBranch" class="flex flex-col gap-4">
      <div>
        <label class="block text-sm font-medium">Nama Cabang</label>
        <input 
          v-model="branch.name" 
          type="text" 
          class="w-full border rounded-lg p-2" 
          required 
        />
      </div>

      <button 
        type="submit" 
        class="bg-[#10375C] hover:bg-[#0c2b49] text-white px-4 py-2 rounded-lg"
      >
        Simpan
      </button>
    </form>
  </div>
</template>

<script setup>
import { ref } from "vue";
import axios from "axios";

const apiBaseUrl = import.meta.env.VITE_API_BASE_URL;
const token = localStorage.getItem("api_token");

const branch = ref({
  name: ""
});

const emit = defineEmits(["saved"]);

const saveBranch = async () => {
  try {
    await axios.post(`${apiBaseUrl}/api/addbranches`, branch.value, {
      headers: { Authorization: `Bearer ${token}` }
    });
    alert("Cabang berhasil ditambahkan!");
    emit("saved");
    branch.value.name = ""; // reset form
  } catch (err) {
    console.error("Error save branch:", err);
    alert("Gagal menambahkan cabang");
  }
};
</script>
