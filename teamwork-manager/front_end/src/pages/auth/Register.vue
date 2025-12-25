<template>
  <div class="auth-bg">
    <div class="auth-card animate-fade">

      <div class="auth-left">
        <h1>TeamWork</h1>
        <p>
          Tạo tài khoản miễn phí và
          bắt đầu làm việc nhóm hiệu quả.
        </p>

         <ul>
          <li>📋 Phân công nhiệm vụ theo nhóm</li>
          <li>⏰ Theo dõi deadline & tiến độ</li>
          <li>💬 Chat nhóm & nộp bài</li>
          <li>🤖 Trợ lý AI gợi ý thông minh</li>
        </ul>
      </div>

      <div class="auth-right">
        <h3>Đăng ký</h3>

        <div v-if="error" class="error-box">
          {{ error }}
        </div>

        <form @submit.prevent="handleRegister">
          <div class="input-group">
            <input v-model="name" required />
            <label>Họ và tên</label>
          </div>

          <div class="input-group">
            <input v-model="email" type="email" required />
            <label>Email</label>
          </div>

          <div class="input-group">
            <input v-model="password" type="password" required />
            <label>Mật khẩu</label>
          </div>

          <div class="input-group">
            <input v-model="password_confirmation" type="password" required />
            <label>Nhập lại mật khẩu</label>
          </div>

          <button class="btn-primary" :disabled="loading">
            {{ loading ? 'Đang đăng ký...' : 'Đăng ký' }}
          </button>
        </form>

        <p class="switch">
          Đã có tài khoản?
          <router-link to="/login">Đăng nhập</router-link>
        </p>
      </div>

    </div>
  </div>
</template>

<script>
import authApi from '@/api/auth'

export default {
  name: 'Register',

  data() {
    return {
      name: '',
      email: '',
      password: '',
      password_confirmation: '',
      error: '',
      loading: false,
    }
  },

  methods: {
    async handleRegister() {
      this.error = ''
      this.loading = true

      try {
        await authApi.register({
          name: this.name,
          email: this.email,
          password: this.password,
          password_confirmation: this.password_confirmation,
        })

        this.$router.push('/login')

      } catch (e) {
        this.error =
          e.response?.data?.message ||
          Object.values(e.response?.data?.errors || {})[0]?.[0] ||
          'Đăng ký thất bại'
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

.auth-left {
  width: 50%;
  background: #f4f5f7;
  padding: 50px;
}
.auth-left h1 {
  color: #0079bf;
  font-size: 36px;
}

.auth-right {
  width: 50%;
  padding: 50px;
}

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

.btn-primary {
  width: 100%;
  padding: 12px;
  border: none;
  border-radius: 10px;
  background: #0079bf;
  color: white;
  font-weight: bold;
  cursor: pointer;
}

.error-box {
  background: #fee2e2;
  color: #b91c1c;
  padding: 10px;
  border-radius: 8px;
  margin-bottom: 15px;
}

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
