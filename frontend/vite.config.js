import { fileURLToPath, URL } from 'node:url'
import fs from 'fs'
import path from 'path'
import { defineConfig } from 'vite'
import vue from '@vitejs/plugin-vue'
import vueDevTools from 'vite-plugin-vue-devtools'
import tailwindcss from '@tailwindcss/vite'
import vueJsx from "@vitejs/plugin-vue-jsx";


export default defineConfig({
  base: './', // <--- jika ingin di convert ke mobile app ubah menjadi "./" untuk web /frontend/dist/
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
  //Gunakan dibawah ini jika anda ingin melakukan test aplikasi dilocal
  // server: {
  //   https: {
  //     key: fs.readFileSync(path.resolve(__dirname, 'cert/localhost-key.pem')),
  //     cert: fs.readFileSync(path.resolve(__dirname, 'cert/localhost-cert.pem')),
  //   },
  //   host: 'localhost',
  //   port: 5173,
  // },
})
