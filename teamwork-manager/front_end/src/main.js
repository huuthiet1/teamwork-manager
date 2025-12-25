import { createApp } from 'vue'
import App from './App.vue'
import router from './router'

// CSS global (nếu có)
import './style.css'

// API
import authApi from '@/api/auth'

const app = createApp(App)

// ===== CHECK LOGIN KHI LOAD APP =====
const token = localStorage.getItem('token')

if (token) {
  // Nếu có token → thử lấy user
  authApi.me().catch(() => {
    // Token lỗi / hết hạn
    localStorage.removeItem('token')
  })
}

app.use(router)
app.mount('#app')
