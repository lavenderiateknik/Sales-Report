<template>
  <div>
    <h3 class="text-xl font-bold mb-4">Edit Cabang</h3>
    <form @submit.prevent="updateBranch" class="flex flex-col gap-4">
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
        Update
      </button>
    </form>
  </div>
</template>

<script setup>
import { ref, onMounted } from "vue";
import axios from "axios";

const props = defineProps({
  branchId: { type: Number, required: true }
});

const apiBaseUrl = import.meta.env.VITE_API_BASE_URL;
const token = localStorage.getItem("api_token");

const branch = ref({
  name: ""
});

const emit = defineEmits(["saved"]);

onMounted(async () => {
  try {
    const res = await axios.get(`${apiBaseUrl}/api/branches/${props.branchId}`, {
      headers: { Authorization: `Bearer ${token}` }
    });
      branch.value = res.data.data;
      
  } catch (err) {
    console.error("Error load branch:", err);
  }
});

const updateBranch = async () => {
  try {
    await axios.put(`${apiBaseUrl}/api/updatebranch/${props.branchId}`, branch.value, {
      headers: { Authorization: `Bearer ${token}` }
    });
    alert("Cabang berhasil diupdate!");
    emit("saved");
  } catch (err) {
    console.error("Error update branch:", err);
    alert("Gagal mengupdate cabang");
  }
};
</script>
