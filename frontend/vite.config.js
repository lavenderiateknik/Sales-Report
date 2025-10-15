import { fileURLToPath, URL } from 'node:url'
import fs from 'fs'
import { defineConfig } from 'vite'
import vue from '@vitejs/plugin-vue'
import vueDevTools from 'vite-plugin-vue-devtools'
import tailwindcss from '@tailwindcss/vite'
import vueJsx from "@vitejs/plugin-vue-jsx";

export default defineConfig({
  base: '/frontend/dist/', // <--- ubah dari './' jika blank di refresh
  plugins: [
    vue(),
    vueDevTools(),
    tailwindcss(),
    vueJsx(),
  ],
  resolve: {
    alias: {
      '@': fileURLToPath(new URL('./src', import.meta.url))
    },
  },
})
