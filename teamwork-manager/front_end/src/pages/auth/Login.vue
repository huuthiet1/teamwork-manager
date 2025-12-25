<template>
  <div class="auth-bg">
    <div class="auth-card animate-fade">

      <!-- LEFT -->
      <div class="auth-left">
        <h1>TeamWork</h1>
        <p>
          Quản lý công việc nhóm đơn giản,
          minh bạch và hiệu quả.
        </p>

         <ul>
          <li>📋 Quản lý nhiệm vụ theo nhóm</li>
          <li>💬 Chat nhóm gần thời gian thực</li>
          <li>🤖 Trợ lý AI gợi ý thông minh</li>
          <li>📊 Thống kê & báo cáo PDF</li>
        </ul>
      </div>

      <!-- RIGHT -->
      <div class="auth-right">
        <h3>Đăng nhập</h3>

        <div v-if="error" class="error-box">
          {{ error }}
        </div>

        <form @submit.prevent="handleLogin">
          <div class="input-group">
            <input v-model="email" type="email" required />
            <label>Email</label>
          </div>

          <div class="input-group">
            <input v-model="password" type="password" required />
            <label>Mật khẩu</label>
          </div>

          <button class="btn-primary" :disabled="loading">
            {{ loading ? 'Đang đăng nhập...' : 'Đăng nhập' }}
          </button>
        </form>

        <p class="switch">
          Chưa có tài khoản?
          <router-link to="/register">Đăng ký</router-link>
        </p>
      </div>

    </div>
  </div>
</template>

<script>
import authApi from '@/api/auth'

export default {
  name: 'Login',

  data() {
    return {
      email: '',
      password: '',
      error: '',
      loading: false,
    }
  },

  methods: {
    async handleLogin() {
      this.error = ''
      this.loading = true

      try {
        const res = await authApi.login({
          email: this.email,
          password: this.password,
        })

        localStorage.setItem('token', res.data.token)
        this.$router.push('/dashboard')

      } catch (e) {
        this.error =
          e.response?.data?.message ||
          'Đăng nhập thất bại'
      } finally {
        this.loading = false
      }
    },
  },
}
</script>

<style scoped>
.auth-bg {
  min-height: 100vh;
  background: linear-gradient(135deg, #0079bf, #5fa8d3);
  display: flex;
  justify-content: center;
  align-items: center;
}

.auth-card {
  width: 900px;
  display: flex;
  background: white;
  border-radius: 16px;
  overflow: hidden;
  box-shadow: 0 25px 60px rgba(0,0,0,.25);
}

/* LEFT */
.auth-left {
  width: 50%;
  background: #f4f5f7;
  padding: 50px;
}
.auth-left h1 {
  color: #0079bf;
  font-size: 36px;
}
.auth-left ul {
  margin-top: 30px;
  padding-left: 20px;
}

/* RIGHT */
.auth-right {
  width: 50%;
  padding: 50px;
}
.auth-right h3 {
  margin-bottom: 25px;
}

/* INPUT */
.input-group {
  position: relative;
  margin-bottom: 25px;
}
.input-group input {
  width: 100%;
  padding: 12px;
  border: 1px solid #cbd5e1;
  border-radius: 8px;
  outline: none;
}
.input-group label {
  position: absolute;
  left: 12px;
  top: 50%;
  transform: translateY(-50%);
  color: #94a3b8;
  transition: .3s;
  background: white;
  padding: 0 5px;
}
.input-group input:focus + label,
.input-group input:valid + label {
  top: -8px;
  font-size: 12px;
  color: #0079bf;
}

/* BUTTON */
.btn-primary {
  width: 100%;
  padding: 12px;
  border: none;
  border-radius: 10px;
  background: #0079bf;
  color: white;
  font-weight: bold;
  cursor: pointer;
  transition: .3s;
}
.btn-primary:hover {
  background: #026aa7;
}

/* ERROR */
.error-box {
  background: #fee2e2;
  color: #b91c1c;
  padding: 10px;
  border-radius: 8px;
  margin-bottom: 15px;
}

/* ANIMATION */
.animate-fade {
  animation: fadeIn .6s ease;
}
@keyframes fadeIn {
  from { opacity: 0; transform: translateY(20px); }
  to { opacity: 1; transform: none; }
}

/* MOBILE */
@media (max-width: 768px) {
  .auth-card {
    flex-direction: column;
    width: 95%;
  }
  .auth-left,
  .auth-right {
    width: 100%;
  }
}
</style>
