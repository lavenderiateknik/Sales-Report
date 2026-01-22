<template>
    <div class="bg-[#10375C]/10 mx-4 my-4 px-4 py-2 rounded-2xl">
        <div class="flex flex-row items-center py-2 px-4 text-xl text-slate-600 gap-1">
            <UserPen class=" h-8 w-8" />
            <span>Edit</span>
            <strong class="ml-2">Profile</strong>
        </div>
        <EditUserForm
            :user-id="editingUserId" 
            @saved="handleEditSaved"
        />
    </div>
</template>

<script setup>
import { UserPen } from 'lucide-vue-next';
import EditUserForm from './EditUserForm.vue';

const editingUserId =localStorage.getItem("id");


</script>

<style lang="scss" scoped>

</style>