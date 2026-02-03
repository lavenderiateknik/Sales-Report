<template>
  <div class="font-sans">
    <div class="relative min-h-screen flex flex-col justify-center items-center bg-[#F4F6FF] px-4 sm:px-6 lg:px-8">
      <div class="relative w-full max-w-xs">
        <div class="card bg-[#10375C] shadow-lg w-full h-full rounded-3xl absolute transform -rotate-6"></div>
        <div class="card bg-[#F3C623] shadow-lg w-full h-full rounded-3xl absolute transform rotate-6"></div>

        <div class="relative w-full rounded-3xl px-6 py-6 sm:py-8 bg-[#F4F6FF] border-2 border-[#F3C623] shadow-lg shadow-slate-700">
          <img
            src="../assets/img/flat version.png"
            class="h-28 sm:h-40 md:h-44 lg:h-46 mx-auto object-contain mb-3"
            alt="logo"
          />

          <label
            class="bg-[#EB8317] px-1 py-1 shadow-md rounded-lg block mt-0 text-base sm:text-lg md:text-xl text-gray-700 text-left"
          >
            Sales <br />
            <span class="font-semibold text-white">Report System</span>
          </label>

          <form class="mt-8 space-y-6" @submit.prevent="login">
            <div>
              <input
                type="email"
                placeholder="Email"
                v-model="email" class="block w-full h-11 rounded-xl px-3 bg-gray-100 shadow-md border border-gray-200 focus:bg-blue-50 focus:ring focus:ring-blue-200 focus:outline-none text-sm md:text-base"
              />
            </div>

            <div>
              <input
                type="password"
                placeholder="Password"
                v-model="password" class="block w-full h-11 rounded-xl px-3 bg-gray-100 shadow-md border border-gray-200 focus:bg-blue-50 focus:ring focus:ring-blue-200 focus:outline-none text-sm md:text-base"
              />
            </div>

            <div>
              <button
                type="submit"
                class="w-full py-3 rounded-xl text-white bg-blue-500 shadow-md hover:bg-blue-600 hover:shadow-lg focus:ring focus:ring-blue-300 transition duration-300 ease-in-out transform hover:-translate-y-0.5 hover:scale-[1.02] text-sm md:text-base"
              >
                Login
              </button>
            </div>
            <div v-if="loginError" class="text-red-500 text-center mt-2">{{ loginError }}</div>
          </form>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue';
import { useRouter } from "vue-router";
import axios from 'axios';

const router = useRouter();
const email = ref('');
const password = ref('');
const loginError = ref(null);

const apiBaseUrl = import.meta.env.VITE_API_BASE_URL;

const login = async () => {
  loginError.value = null;

 try {
    const response = await axios.post(`${apiBaseUrl}/api/login`, {
      email: email.value,
      password: password.value,
    });

    const userData = response.data;
    console.log("Cek Data User:", userData); // Lihat di console structure-nya

    // Gunakan Optional Chaining (?.) agar jika data null, aplikasi tidak crash
    localStorage.setItem('api_token', userData?.token || '');
    localStorage.setItem('id', userData?.user?.id || '');
    localStorage.setItem('name', userData?.user?.name || '');
    localStorage.setItem('email', userData?.user?.email || '');
    localStorage.setItem('role', userData?.user?.role_id || '');
    
    // Bagian yang paling sering bikin error 'data of undefined'
    localStorage.setItem('role_name', userData?.user?.role?.name || 'No Role');
    localStorage.setItem('branch', userData?.user?.branch_id || '');
    localStorage.setItem('branch_name', userData?.user?.branch?.name || 'No Branch');

    await new Promise(resolve => setTimeout(resolve, 100));

    window.location.href = '/';

  } catch (error) {
    console.error("Detail Error:", error); 

    
    if (error.response && error.response.data) {
      
      loginError.value = error.response.data.message || 'Email atau password salah.';
    } else if (error.request) {
      loginError.value = 'Tidak ada respon dari server. Periksa koneksi internet atau URL API.';
    } else {
      
      loginError.value = 'Terjadi kesalahan sistem.';
    }
  }
};
</script>

<style scoped>
.card {
  top: 0;
  left: 0;
}
</style>