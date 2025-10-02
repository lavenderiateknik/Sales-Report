import { fileURLToPath, URL } from 'node:url'

import fs from 'fs'
import { defineConfig } from 'vite'
import vue from '@vitejs/plugin-vue'
import vueDevTools from 'vite-plugin-vue-devtools'
import tailwindcss from '@tailwindcss/vite'
import vueJsx from "@vitejs/plugin-vue-jsx";

// https://vite.dev/config/
export default defineConfig({
  base: '/frontend/dist/',
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
  // server: {
  //   host: '0.0.0.0',   // biar bisa diakses dari device lain
  //   port: 5173,        // bisa ganti kalau bentrok
  //    https: {
  //     key: fs.readFileSync('./cert/vite.key'),
  //     cert: fs.readFileSync('./cert/vite.crt'),
  //   }
  // }
})
