import './assets/main.css'

import "vue3-easy-data-table/dist/style.css";
import "@/assets/easy-data-table.css";


import { createApp } from 'vue'
import App from './App.vue'
import router from './router'

const app = createApp(App)

app.use(router)

app.mount('#app')
