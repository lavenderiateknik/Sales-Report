<template>
  <div class="font-sans">
    <div class="relative min-h-screen flex flex-col justify-center items-center bg-[#F4F6FF] px-4 sm:px-6 lg:px-8">
      <div class="relative w-full max-w-xs">
        <!-- background cards -->
        <div class="card bg-[#10375C] shadow-lg w-full h-full rounded-3xl absolute transform -rotate-6"></div>
        <div class="card bg-[#F3C623] shadow-lg w-full h-full rounded-3xl absolute transform rotate-6"></div>

        <!-- form card -->
        <div class="relative w-full rounded-3xl px-6 py-6 sm:py-8 bg-[#F4F6FF] border-2 border-[#F3C623] shadow-lg shadow-slate-700">
          <!-- logo / image -->
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

          <!-- form -->
          <form class="mt-8 space-y-6" @submit.prevent="login">
            <div>
              <input
                type="email"
                placeholder="User Name"
                class="block w-full h-11 rounded-xl px-3 bg-gray-100 shadow-md border border-gray-200 focus:bg-blue-50 focus:ring focus:ring-blue-200 focus:outline-none text-sm md:text-base"
              />
            </div>

            <div>
              <input
                type="password"
                placeholder="Password"
                class="block w-full h-11 rounded-xl px-3 bg-gray-100 shadow-md border border-gray-200 focus:bg-blue-50 focus:ring focus:ring-blue-200 focus:outline-none text-sm md:text-base"
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
          </form>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { useRouter } from "vue-router";

const router = useRouter();

const login = () => {
  localStorage.setItem("auth", "true");
  router.push({ name: "home" });
};
</script>

<style scoped>
.card {
  top: 0;
  left: 0;
}
</style>
