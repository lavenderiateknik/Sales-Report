<template>
  <div class="flex flex-col w-full">
    <!-- Header: judul + hamburger sejajar -->
    <div class="flex items-center justify-between p-3">
      <div class="p-2 rounded-2xl bg-[#F4F6FF] flex items-center gap-2 shadow-md shadow-[#EB8317]">
        <img src="../assets/img/flat version.png" class="w-12" alt="">
        <span class="text-base sm:text-lg md:text-xl lg:text-base text-slate-600">
          System <strong>Report Sales</strong>
        </span>
      </div>
      
      <!-- Tombol Menu (hanya mobile) -->
      <button @click="isOpen = !isOpen" class="md:hidden">
        <Menu v-if="!isOpen" class="w-6 h-6 text-[#F3C623]" />
        <X v-else class="w-6 h-6 text-[#F3C623]" />
      </button>
    </div>

    <!-- User Data-->
    <div class="flex flex-row gap-2 items-center lg:items-start mx-2 mb-2 rounded-xl px-4 py-4 text-sm text-slate-600 bg-slate-50 border-2 border-[#F3C623] shadow-amber-50 shadow-sm">
      <Contact class="w-10 h-10" />
      <div class="flex flex-col">
        <span>Welcome,</span>
        <strong class="ml-0">{{ userName }}</strong>
        <strong class="ml-0 uppercase text-[10px]">{{ userRole }}</strong>
        <strong v-if="userRole>4" class="ml-0">{{ userBranch }}</strong>
      </div>
    </div>

    <!-- Navigasi (satu untuk semua) -->
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
          <RouterLink 
            to="/sales"  
            class="text-sm pl-5 flex items-center gap-1 pb-1 my-1 hover:font-semibold"
            exact-active-class="bg-blue-200 bg-opacity-10 font-semibold"
            @click="closeMobileMenu"
          >
            <ChartNoAxesCombined /> Sales Dashboard
          </RouterLink>
          <RouterLink 
            to="/customer"  
            class="text-sm pl-5 flex items-center gap-1 pb-1 my-1 hover:font-semibold"
            exact-active-class="bg-blue-200 bg-opacity-10 font-semibold"
            @click="closeMobileMenu"
          >
            <NotebookTabs /> Customer Dashboard
          </RouterLink>
          <RouterLink 
            to="/spvdashboard" 
            class="text-sm pl-5 flex items-center gap-1 pb-1 my-1 hover:font-semibold"
            exact-active-class="bg-blue-200 bg-opacity-10 font-semibold"
            @click="closeMobileMenu"
          >
            <ScrollText /> SPV Rekap
          </RouterLink>
        </div>

        <div class="text-[#10375C] ">
          <span class="text-sm px-2 font-semibold">FUNCTION</span>
          <RouterLink 
            to="/checkin" 
            class="text-sm pl-5 flex items-center gap-1 pb-1 my-1 hover:font-semibold"
            exact-active-class="bg-blue-200 bg-opacity-10 font-semibold"
            @click="closeMobileMenu"
          >
            <LandPlot /> Report Sales
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
import { Menu, X, Home, NotebookTabs, ScrollText, LogOut, LandPlot, Contact, ChartNoAxesCombined } from "lucide-vue-next";
import axios from 'axios';

// Variabel untuk menyimpan data pengguna
const userName = ref('');
const userEmail = ref('');
const userId = ref(null);
const userRole = ref('');
const userBranch = ref('');
const apiBaseUrl = import.meta.env.VITE_API_BASE_URL;

// Ambil data pengguna dari API yang terproteksi
const fetchUserData = async () => {
    try {
        const token = localStorage.getItem('api_token');
        if (token) {
            const response = await axios.get(apiBaseUrl+`/api/user`, {
                headers: {
                    'Authorization': `Bearer ${token}`
                }
            });
            const user = response.data.data;
            userName.value = user.name;
            userEmail.value = user.email;
            userId.value = user.id;
            userRole.value = user.role.name;
            userBranch.value = user.branch.name;
            
        }
    } catch (error) {
        console.error("Gagal mengambil data user:", error);
    }
};

const isOpen = ref(false);
const isDesktop = ref(false);
const router = useRouter();

// deteksi lebar layar (supaya nav selalu tampil di desktop)
const checkScreen = () => {
  isDesktop.value = window.innerWidth >= 768; // md breakpoint
  if (isDesktop.value) isOpen.value = false; // reset toggle saat desktop
};

// fungsi logout
const logout = async () => {
  try {
    const token = localStorage.getItem("api_token");
    if (token) {
      await axios.post(
        `${import.meta.env.VITE_API_BASE_URL}/api/logout`,
        {},
        {
          headers: { 'Authorization': `Bearer ${token}` }
        }
      );
    }
    localStorage.removeItem("api_token");
    router.push({ name: "Login" });
  } catch (error) {
    console.error("Logout gagal:", error);
    localStorage.removeItem("api_token");
    router.push({ name: "Login" });
  }
};

// Tutup menu jika mobile
const closeMobileMenu = () => {
  if (!isDesktop.value) {
    isOpen.value = false;
  }
};

// Logout + tutup menu
const handleLogout = async () => {
  await logout();
  closeMobileMenu();
};

const getUserRole = async () => {
  try {
    const token = localStorage.getItem("api_token");
  } catch (error) {
    
  }
}

onMounted(() => {
  checkScreen();
  window.addEventListener("resize", checkScreen);
  fetchUserData();
});

onUnmounted(() => {
  window.removeEventListener("resize", checkScreen);
});
</script>

<style scoped>
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.2s ease;
}

.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}
</style>
