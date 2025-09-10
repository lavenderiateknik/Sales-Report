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

    <!-- Navigasi (satu untuk semua) -->
    <transition name="fade" class="shadow-md/60 shadow-slate-50">
      <nav v-show="isOpen || isDesktop"
        class="flex flex-col gap-2 bg-[#F4F6FF] mx-2 rounded-lg mb-3 py-3 border-2 border-[#EB8317]">
        <div class="text-[#10375C] ">
          <span class="text-sm px-2 font-semibold">DASHBOARD</span>
          <RouterLink to="/" class="text-sm pl-5 flex items-center gap-1 pb-1 my-1 hover:font-semibold"
            exact-active-class="bg-blue-200 bg-opacity-10 font-semibold">
            <Home /> Sales Dashboard
          </RouterLink>
          <RouterLink to="/customer"  class="text-sm pl-5 flex items-center gap-1 pb-1 my-1 hover:font-semibold"
            exact-active-class="bg-blue-200 bg-opacity-10 font-semibold">
            <NotebookTabs />Customer Dashboard
          </RouterLink>
          <RouterLink to="/spvdashboard" class="text-sm pl-5 flex items-center gap-1 pb-1 my-1 hover:font-semibold"
            exact-active-class="bg-blue-200 bg-opacity-10 font-semibold">
            <ScrollText />SPV Rekap
          </RouterLink>
        </div>
        <div class="text-[#10375C] ">
          <span class="text-sm px-2 font-semibold">FUNCTION</span>
          <RouterLink to="/checkin" class="text-sm pl-5 flex items-center gap-1 pb-1 my-1 hover:font-semibold"
            exact-active-class="bg-blue-200 bg-opacity-10 font-semibold">
            <LandPlot />Report Sales
          </RouterLink>
        </div>
        <div class="text-[#10375C] cursor-pointer" @click="logout()">
          <span class="text-sm px-2 font-semibold flex flex-row gap-1">
            <LogOut />LOGOUT
          </span>
        </div>
      </nav>
    </transition>
  </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from "vue";
import { useRouter } from "vue-router";
import { Menu, X, Home, NotebookTabs, ScrollText, LogOut, LandPlot } from "lucide-vue-next";

const isOpen = ref(false);
const isDesktop = ref(false);
const router = useRouter();


// deteksi lebar layar (supaya nav selalu tampil di desktop)
const checkScreen = () => {
  isDesktop.value = window.innerWidth >= 768; // md breakpoint
  if (isDesktop.value) isOpen.value = false; // reset toggle saat desktop
};

// fungsi logout
const logout = () => {
  localStorage.removeItem("auth");
  router.push({ name: "Login" });
};

onMounted(() => {
  checkScreen();
  window.addEventListener("resize", checkScreen);
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
