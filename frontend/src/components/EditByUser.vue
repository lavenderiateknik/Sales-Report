<template>
  <div class="bg-[#10375C]/10 mx-4 my-4 px-4 py-2 rounded-2xl">
    <div class="flex flex-row items-center py-2 px-4 text-xl text-slate-600 gap-1">
      <UserPen class="h-8 w-8" />
      <span>Edit</span>
      <strong class="ml-2">Profile</strong>
    </div>

    <EditUserForm
      :user-id="editingUserId"
      :role="role"
      @saved="handleEditSaved"
    />
  </div>
</template>

<script setup>
import { ref } from 'vue';
import { UserPen } from 'lucide-vue-next';
import EditUserForm from './EditUserForm.vue';

// --- Ambil ID user dan role dari localStorage ---
const editingUserId = ref(Number(localStorage.getItem('id')));
const role = ref(Number(localStorage.getItem('role')));

// --- Event handler ketika form selesai disimpan ---
const handleEditSaved = () => {
 
  alert('User berhasil diupdate!');
 
};
</script>

<style scoped></style>
