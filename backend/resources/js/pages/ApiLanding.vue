<script setup>
const apiInfo = {
  name: "Sales Report API",
  version: "1.0.0",
  status: "OK",
  docsUrl: "/api/docs"
}
</script>

<template>
  <div class="min-h-screen flex flex-col items-center justify-center bg-gray-100 p-6">
    <div class="bg-white shadow-xl rounded-2xl p-10 max-w-lg text-center space-y-4">
      <h1 class="text-3xl font-bold text-blue-600">
        {{ apiInfo.name }}
      </h1>
      <p class="text-gray-700">Version: {{ apiInfo.version }}</p>
      <p class="text-green-600 font-semibold">Status: {{ apiInfo.status }}</p>

    </div>
  </div>
</template>
