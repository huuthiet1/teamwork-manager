<template>
  <div class="change-password">
    <div class="card">
      <h1>🔒 Đổi mật khẩu</h1>
      <p class="sub">
        Vì lý do bảo mật, bạn sẽ cần đăng nhập lại sau khi đổi mật khẩu
      </p>

      <div v-if="error" class="error">
        {{ error }}
      </div>

      <form @submit.prevent="submit">
        <div class="field">
          <label>Mật khẩu hiện tại</label>
          <input
            v-model="current_password"
            type="password"
            required
          />
        </div>

        <div class="field">
          <label>Mật khẩu mới</label>
          <input
            v-model="new_password"
            type="password"
            minlength="8"
            required
          />
        </div>

        <div class="field">
          <label>Xác nhận mật khẩu mới</label>
          <input
            v-model="new_password_confirmation"
            type="password"
            required
          />
        </div>

        <button class="btn primary" :disabled="loading">
          {{ loading ? 'Đang cập nhật...' : 'Đổi mật khẩu' }}
        </button>
      </form>
    </div>
  </div>
</template>

<script>
import authApi from '@/api/auth'

export default {
  name: 'ChangePassword',

  data() {
    return {
      current_password: '',
      new_password: '',
      new_password_confirmation: '',
      loading: false,
      error: '',
    }
  },

  methods: {
    async submit() {
      this.error = ''
      this.loading = true

      if (
        this.new_password !==
        this.new_password_confirmation
      ) {
        this.error = 'Mật khẩu xác nhận không khớp'
        this.loading = false
        return
      }

      try {
        await authApi.changePassword({
          current_password: this.current_password,
          new_password: this.new_password,
          new_password_confirmation:
            this.new_password_confirmation,
        })

        alert(
          'Đổi mật khẩu thành công, vui lòng đăng nhập lại'
        )
        localStorage.clear()
        this.$router.push('/login')
      } catch (e) {
        this.error =
          e.response?.data?.message ||
          'Không thể đổi mật khẩu'
      } finally {
        this.loading = false
      }
    },
  },
}
</script>

<style scoped>
.change-password {
  max-width: 520px;
  margin: 0 auto;
}

/* CARD */
.card {
  background: white;
  padding: 28px;
  border-radius: 16px;
}

/* TEXT */
h1 {
  font-size: 24px;
  font-weight: 800;
}
.sub {
  color: #64748b;
  font-size: 14px;
  margin-bottom: 20px;
}

/* FORM */
.field {
  margin-bottom: 16px;
}
label {
  display: block;
  font-weight: 700;
  margin-bottom: 6px;
}
input {
  width: 100%;
  padding: 12px;
  border-radius: 10px;
  border: 1px solid #cbd5e1;
}

/* ERROR */
.error {
  background: #fee2e2;
  color: #dc2626;
  padding: 10px;
  border-radius: 10px;
  margin-bottom: 14px;
}

/* BUTTON */
.btn.primary {
  width: 100%;
  padding: 12px;
  border-radius: 12px;
  background: #2563eb;
  color: white;
  font-weight: 800;
  border: none;
  cursor: pointer;
}
.btn.primary:hover {
  opacity: .9;
}
</style>
