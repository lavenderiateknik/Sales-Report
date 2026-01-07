<template>
  <div class="flex flex-col w-full pt-4">
    <!-- Header -->
    <div class="flex items-center justify-between p-3 ">
      <div class="p-2 rounded-2xl bg-[#F4F6FF] flex items-center gap-2 shadow-md shadow-[#EB8317]">
        <img src="../assets/img/flat version.png" class="w-12" alt="">
        <span class="text-base sm:text-lg md:text-xl lg:text-base text-slate-600">
          System <br /><strong>Report Sales</strong>
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

          <div class="text-[#10375C] ">
            <span class="text-sm px-2 font-semibold">DASHBOARD</span>

            <!-- Dashboard basic start -->
            <RouterLink to="/" class="text-sm pl-5 flex items-center gap-1 pb-1 my-1 hover:font-semibold"
              exact-active-class="bg-blue-200 bg-opacity-10 font-semibold" @click="closeMobileMenu">
              <Home />Dashboard
            </RouterLink>
            <RouterLink to="/recapreport" class="text-sm pl-5 flex items-center gap-1 pb-1 my-1 hover:font-semibold"
              exact-active-class="bg-blue-200 bg-opacity-10 font-semibold" @click="closeMobileMenu">
              <BookOpenText /> Daily Visit
            </RouterLink>
            <!-- Dashboard basic end -->
            <!-- Dashboard Sales Manager Start -->
            <RouterLink to="/customer" v-if="userRoleId == 4 | userRoleId == 3 | userRoleId == 1 | userRoleId == 1"
              class="text-sm pl-5 flex items-center gap-1 pb-1 my-1 hover:font-semibold"
              exact-active-class="bg-blue-200 bg-opacity-10 font-semibold" @click="closeMobileMenu">
              <NotebookTabs /> Customer History
            </RouterLink>
            <RouterLink to="revenue" v-if="userRoleId == 3 | userRoleId == 2 | userRoleId == 1"
              class="text-sm pl-5 flex items-center gap-1 pb-1 my-1 hover:font-semibold"
              exact-active-class="bg-blue-200 bg-opacity-10 font-semibold" @click="closeMobileMenu">
              <Medal /> Revenue
            </RouterLink>

            <RouterLink v-if="userRoleId == 3 | userRoleId == 2 | userRoleId == 1"
              to="/assignmentprecentage" class="text-sm pl-5 flex items-center gap-1 pb-1 my-1 hover:font-semibold"
              exact-active-class="bg-blue-200 bg-opacity-10 font-semibold" @click="closeMobileMenu">
              <ChartPie /> Assignment Precentage
            </RouterLink>



            <!-- KPI Parent -->
             <!-- v-if="[4, 5, 6,] -->
            <div v-if="userRoleId == 3|userRoleId == 2|userRoleId == 1"
              class="text-sm pl-5 flex items-center justify-between gap-1 pb-1 my-1 cursor-pointer hover:font-semibold"
              @click="isKpiOpen = !isKpiOpen">
              <div class="flex items-center gap-2">
                <Ruler />
                <span>KPI</span>
              </div>
              <ChevronDown v-if="isKpiOpen" class="w-4 h-4" />
              <ChevronRight v-else class="w-4 h-4" />
            </div>

            <!-- KPI Child -->
            <transition name="fade">
              <div v-show="isKpiOpen" class="ml-8 flex flex-col gap-2">
                <RouterLink to="/kpi" class="link flex gap-2"
                  exact-active-class="bg-blue-200 bg-opacity-10 font-semibold" @click="closeMobileMenu">
                  
                  <FileChartColumnIncreasing />
                  Sales KPI
                </RouterLink>

                <RouterLink to="/targetkpilist" class="link flex gap-2"
                  exact-active-class="bg-blue-200 bg-opacity-10 font-semibold" @click="closeMobileMenu">
                  <ListChecksIcon />
                  Target KPI
                </RouterLink>

                <RouterLink to="/attendance" class="link flex gap-2"
                  exact-active-class="bg-blue-200 bg-opacity-10 font-semibold" @click="closeMobileMenu">
                  <CalendarCheck />
                  Attendance
                </RouterLink>
              </div>
            </transition>




            <RouterLink v-if="userRoleId !== 7" to="/assignment"
              class="text-sm pl-5 flex items-center gap-1 pb-1 my-1 hover:font-semibold"
              exact-active-class="bg-blue-200 bg-opacity-10 font-semibold" @click="closeMobileMenu">
              <Handshake /> Assignment
            </RouterLink>

            <!-- SPV Dashboard Parent -->
            <div v-if="userRoleId == 6 | userRoleId == 5 | userRoleId == 3 | userRoleId == 1 | userRoleId == 1"
              class="text-sm pl-5 flex items-center justify-between gap-1 pb-1 my-1 cursor-pointer hover:font-semibold"
              @click="isSpvOpen = !isSpvOpen">
              <div class="flex items-center gap-">
                <ScrollText />
                <span>SPV Dashboard</span>
              </div>
              <ChevronDown v-if="isSpvOpen" class="w-4 h-4" />
              <ChevronRight v-else class="w-4 h-4" />
            </div>
            <transition name="fade">
              <div v-show="isSpvOpen" class="ml-8 flex flex-col gap-2">
                <!-- PERSONAL REPORT -->
                <div class="flex items-center justify-between cursor-pointer pl-1 gap-1 hover:font-semibold"
                  @click="isPersonalOpen = !isPersonalOpen">
                  <span class="flex gap-2">
                    <BookUser />
                    Personal Report
                  </span>
                  <ChevronDown v-if="isPersonalOpen" class="w-4 h-4" />
                  <ChevronRight v-else class="w-4 h-4" />
                </div>

                <div v-show="isPersonalOpen" class="ml-6 flex flex-col gap-1">
                  <RouterLink to="/personal-report/recaptype" class="link flex gap-2"
                    exact-active-class="bg-blue-200 bg-opacity-10 font-semibold" @click="closeMobileMenu">
                    <ListChecksIcon />Recapitulation
                  </RouterLink>
                </div>
                <!-- TEAM REPORT -->
                <div class="flex items-center justify-between cursor-pointer pl-1 gap-1 hover:font-semibold"
                  @click="isTeamOpen = !isTeamOpen">
                  <span class="flex gap-2">
                    <Users2 />
                    Grup Report
                  </span>
                  <ChevronDown v-if="isTeamOpen" class="w-4 h-4" />
                  <ChevronRight v-else class="w-4 h-4" />
                </div>
                <div v-show="isTeamOpen" class="ml-6 flex flex-col gap-1">
                  <RouterLink to="/group-report/recap" class="link flex gap-1"
                    exact-active-class="bg-blue-200 bg-opacity-10 font-semibold" @click="closeMobileMenu">
                    <ListChecksIcon />
                    Recapitulation
                  </RouterLink>
                  <!-- Report Data nested -->
                  <div class="flex items-center justify-between cursor-pointer pl-0 gap-1 hover:font-semibold"
                    @click="isReportDataOpen = !isReportDataOpen">
                    <span class="flex gap-2">
                      <ClipboardListIcon />Report Data
                    </span>
                    <ChevronDown v-if="isReportDataOpen" class="w-4 h-4" />
                    <ChevronRight v-else class="w-4 h-4" />
                  </div>
                  <div v-show="isReportDataOpen" class="ml-6 flex flex-col gap-1">
                    <RouterLink to="/recap-customer/bci" class="link"
                      exact-active-class="bg-blue-200 bg-opacity-10 font-semibold" @click="closeMobileMenu">
                      BCI
                    </RouterLink>
                    <RouterLink to="/recap-customer/reg" exact-active-class="bg-blue-200 bg-opacity-10 font-semibold"
                      class="link" @click="closeMobileMenu">
                      REGULER
                    </RouterLink>
                  </div>
                </div>
              </div>
            </transition>
          </div>
        </div>
        <div class="text-[#10375C] ">
          <span class="text-sm px-2 font-semibold">MENU</span>
          <RouterLink to="/optionreport" class="text-sm pl-5 flex items-center gap-1 pb-1 my-1 hover:font-semibold"
            exact-active-class="bg-blue-200 bg-opacity-10 font-semibold" @click="closeMobileMenu">
            <LandPlot /> Report Sales
          </RouterLink>
          <RouterLink v-if="![4, 5, 6, 7, 8].includes(userRoleId)" to="/adduser"
            class="text-sm pl-5 flex items-center gap-1 pb-1 my-1 hover:font-semibold"
            exact-active-class="bg-blue-200 bg-opacity-10 font-semibold" @click="closeMobileMenu">
            <UserRoundPlus /> Add User
          </RouterLink>
          <RouterLink v-if="![4, 5, 6, 7, 8].includes(userRoleId)" to="/branches"
            class="text-sm pl-5 flex items-center gap-1 pb-1 my-1 hover:font-semibold"
            exact-active-class="bg-blue-200 bg-opacity-10 font-semibold" @click="closeMobileMenu">
            <GitBranch /> Branches
          </RouterLink>
          <RouterLink v-if="![4, 5, 6, 7, 8].includes(userRoleId)" to="/customerdata"
            class="text-sm pl-5 flex items-center gap-1 pb-1 my-1 hover:font-semibold"
            exact-active-class="bg-blue-200 bg-opacity-10 font-semibold" @click="closeMobileMenu">
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
  BookOpenText,
  ChevronDown,
  ChevronRight,
  Handshake,
  BookUser,
  ListChecksIcon,
  Medal,
  Users2,
  ClipboardListIcon,
  ChartPie,
  FileChartColumnIncreasing,
  Ruler,
  CalendarCheck
} from "lucide-vue-next";
import axios from 'axios';

const userName = ref('');
const userEmail = ref('');
const userId = ref(null);
const userRoleId = ref(null);   // simpan id role
const userRoleName = ref('');   // simpan nama role
const userBranch = ref('');
const apiBaseUrl = import.meta.env.VITE_API_BASE_URL;
const isSpvOpen = ref(false);
const isKpiOpen = ref(false);
const isPersonalOpen = ref(false);
const isTeamOpen = ref(false);
const isReportDataOpen = ref(false);


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
    localStorage.removeItem("branch_name");
    localStorage.removeItem("role");
    localStorage.removeItem("name");
    localStorage.removeItem("role_name");
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
