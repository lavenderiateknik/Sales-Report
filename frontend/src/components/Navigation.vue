<template>
  <div class="flex flex-col w-full pt-4">
    <!-- Header -->
    <div class="flex items-center justify-between p-3">
      <div class="p-2 rounded-2xl bg-[#F4F6FF] flex items-center gap-2 shadow-md shadow-[#EB8317]">
        <img src="../assets/img/flat version.png" class="w-12" alt="">
        <span class="text-base sm:text-lg md:text-xl lg:text-base text-slate-600">
          System <strong>Report Sales</strong>
        </span>
      </div>

      <!-- Tombol Menu (hanya mobile) -->
      <button @click="isOpen = !isOpen" class="md:hidden">
        <Menu v-if="!isOpen" class="w-12 h-12 text-[#F3C623]" />
        <X v-else class="w-12 h-12  text-[#F3C623]" />
      </button>
    </div>

    <!-- User Data -->
    <div
      class="flex flex-row gap-2 items-center lg:items-start mx-2 mb-2 rounded-xl px-4 py-4 text-sm text-slate-600 bg-slate-50 border-2 border-[#F3C623] shadow-amber-50 shadow-sm">
      <Contact class="w-10 h-10" />
      <div class="flex flex-col">
        <span>Welcome,</span>
        <strong class="ml-0">{{ userName }}</strong>
        <strong class="ml-0 uppercase text-[10px]">{{ userRoleName }}</strong>
        <strong v-if="userRoleId > 4" class="ml-0">{{ userBranch }}</strong>
      </div>
    </div>

    <!-- Navigasi -->
    <transition name="fade" class="shadow-md/60 shadow-slate-50">
      <nav v-show="isOpen || isDesktop"
        class="flex flex-col gap-2 bg-[#F4F6FF] mx-2 rounded-lg mb-3 py-3 border-2 border-[#EB8317]">
        
        <div class="text-[#10375C] ">
          <span class="text-sm px-2 font-semibold">DASHBOARD</span>
          <RouterLink 
            to="/" 
            class="text-sm pl-5 flex items-center gap-1 pb-1 my-1 hover:font-semibold"
            exact-active-class="bg-blue-200 bg-opacity-10 font-semibold"
            @click="closeMobileMenu"
          >
            <Home />Dashboard
          </RouterLink>

          <!-- Hanya tampil jika role_id ≠ 8 -->
          <RouterLink 
            
            to="/customer"  
            class="text-sm pl-5 flex items-center gap-1 pb-1 my-1 hover:font-semibold"
            exact-active-class="bg-blue-200 bg-opacity-10 font-semibold"
            @click="closeMobileMenu"
          >
            <NotebookTabs /> Customer History
          </RouterLink>
          <RouterLink 
            v-if="userRoleId !== 8"
            to="/recapreport"  
            class="text-sm pl-5 flex items-center gap-1 pb-1 my-1 hover:font-semibold"
            exact-active-class="bg-blue-200 bg-opacity-10 font-semibold"
            @click="closeMobileMenu"
          >
            <BookOpenText /> Daily Visit
          </RouterLink>

          <RouterLink 
            v-if="userRoleId !== 8"
            to="/spvdashboard" 
            class="text-sm pl-5 flex items-center gap-1 pb-1 my-1 hover:font-semibold"
            exact-active-class="bg-blue-200 bg-opacity-10 font-semibold"
            @click="closeMobileMenu"
          >
            <ScrollText /> SPV Rekap
          </RouterLink>
        </div>

        <div class="text-[#10375C] ">
          <span class="text-sm px-2 font-semibold">MENU</span>
          <RouterLink 
            to="/optionreport" 
            class="text-sm pl-5 flex items-center gap-1 pb-1 my-1 hover:font-semibold"
            exact-active-class="bg-blue-200 bg-opacity-10 font-semibold"
            @click="closeMobileMenu"
          >
            <LandPlot /> Report Sales
          </RouterLink>
          <RouterLink 
          v-if="![4,5,6,7,8].includes(userRoleId)"
            to="/adduser" 
            class="text-sm pl-5 flex items-center gap-1 pb-1 my-1 hover:font-semibold"
            exact-active-class="bg-blue-200 bg-opacity-10 font-semibold"
            @click="closeMobileMenu"
          >
            <UserRoundPlus /> Add User
          </RouterLink>
          <RouterLink 
          v-if="![4,5,6,7,8].includes(userRoleId)"
            to="/branches" 
            class="text-sm pl-5 flex items-center gap-1 pb-1 my-1 hover:font-semibold"
            exact-active-class="bg-blue-200 bg-opacity-10 font-semibold"
            @click="closeMobileMenu"
          >
            <GitBranch /> Branches
          </RouterLink>
          <RouterLink 
          v-if="![4,5,6,7,8].includes(userRoleId)"
            to="/customerdata" 
            class="text-sm pl-5 flex items-center gap-1 pb-1 my-1 hover:font-semibold"
            exact-active-class="bg-blue-200 bg-opacity-10 font-semibold"
            @click="closeMobileMenu"
          >
            <Database /> Customer Database
          </RouterLink>
        </div>

        <!-- Logout -->
        <div class="text-[#10375C] cursor-pointer" @click="handleLogout">
          <span class="text-sm px-2 font-semibold flex flex-row gap-1">
            <LogOut /> LOGOUT
          </span>
        </div>
      </nav>
    </transition>
  </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from "vue";
import { useRouter } from "vue-router";
import {
  Menu,
  X,
  Home,
  NotebookTabs,
  ScrollText,
  LogOut,
  LandPlot,
  Contact,
  UserRoundPlus,
  GitBranch,
  Database,
  BookOpenText
} from "lucide-vue-next";
import axios from 'axios';

const userName = ref('');
const userEmail = ref('');
const userId = ref(null);
const userRoleId = ref(null);   // simpan id role
const userRoleName = ref('');   // simpan nama role
const userBranch = ref('');
const apiBaseUrl = import.meta.env.VITE_API_BASE_URL;

const fetchUserData = async () => {
  try {
    const token = localStorage.getItem('api_token');
    if (token) {
      const response = await axios.get(apiBaseUrl + `/api/user`, {
        headers: { 'Authorization': `Bearer ${token}` }
      });
      const user = response.data.data;
      userName.value = user.name;
      userEmail.value = user.email;
      userId.value = user.id;
      userRoleId.value = user.role_id;      // ambil role id
      userRoleName.value = user.role.name;  // ambil role name
      userBranch.value = user.branch?.name ?? '';
    }
  } catch (error) {
    console.error("Gagal mengambil data user:", error);
  }
};

const isOpen = ref(false);
const isDesktop = ref(false);
const router = useRouter();

const checkScreen = () => {
  isDesktop.value = window.innerWidth >= 768;
  if (isDesktop.value) isOpen.value = false;
};

const logout = async () => {
  try {
    const token = localStorage.getItem("api_token");
    if (token) {
      await axios.post(`${apiBaseUrl}/api/logout`, {}, {
        headers: { 'Authorization': `Bearer ${token}` }
      });
    }
    localStorage.removeItem("api_token");
    localStorage.removeItem("id");
    localStorage.removeItem("email");
    localStorage.removeItem("branch");
    localStorage.removeItem("role");
    localStorage.removeItem("name");
    router.push({ name: "Login" });
  } catch (error) {
    console.error("Logout gagal:", error);
    localStorage.removeItem("api_token");
    router.push({ name: "Login" });
  }
};

const closeMobileMenu = () => {
  if (!isDesktop.value) {
    isOpen.value = false;
  }
};

const handleLogout = async () => {
  await logout();
  closeMobileMenu();
};

onMounted(() => {
  checkScreen();
  window.addEventListener("resize", checkScreen);
  fetchUserData();
});

onUnmounted(() => {
  window.removeEventListener("resize", checkScreen);
});
</script>
