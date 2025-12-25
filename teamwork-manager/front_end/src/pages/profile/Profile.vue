<template>
  <div class="profile">
    <div class="card">
      <h1>👤 Hồ sơ cá nhân</h1>
      <p class="sub">
        Quản lý thông tin tài khoản của bạn
      </p>

      <div v-if="error" class="error">
        {{ error }}
      </div>

      <form @submit.prevent="update">
        <!-- NAME -->
        <div class="field">
          <label>Họ và tên</label>
          <input
            v-model="form.name"
            required
          />
        </div>

        <!-- EMAIL -->
        <div class="field">
          <label>Email</label>
          <input
            :value="form.email"
            disabled
          />
        </div>

        <!-- ACTIONS -->
        <div class="actions">
          <button
            class="btn primary"
            :disabled="loading"
          >
            {{ loading ? 'Đang lưu...' : 'Lưu thay đổi' }}
          </button>

          <router-link
            to="/change-password"
            class="btn ghost"
          >
            🔒 Đổi mật khẩu
          </router-link>
        </div>
      </form>
    </div>
  </div>
</template>

<script>
import authApi from '@/api/auth'
import api from '@/api/api'

export default {
  name: 'ProfilePage',

  data() {
    return {
      form: {
        name: '',
        email: '',
      },
      loading: false,
      error: '',
    }
  },

  async mounted() {
    await this.fetchProfile()
  },

  methods: {
    async fetchProfile() {
      try {
        const res = await authApi.me()
        this.form.name = res.data.name
        this.form.email = res.data.email
      } catch {
        this.error = 'Không thể tải hồ sơ'
      }
    },

    async update() {
      this.error = ''
      this.loading = true

      try {
        await api.put('/profile', {
          name: this.form.name,
        })

        alert('Cập nhật thành công')
      } catch (e) {
        this.error =
          e.response?.data?.message ||
          'Cập nhật thất bại'
      } finally {
        this.loading = false
      }
    },
  },
}
</script>

<style scoped>
.profile {
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
input[disabled] {
  background: #f1f5f9;
  color: #64748b;
}

/* ERROR */
.error {
  background: #fee2e2;
  color: #dc2626;
  padding: 10px;
  border-radius: 10px;
  margin-bottom: 14px;
}

/* ACTIONS */
.actions {
  display: flex;
  gap: 12px;
  margin-top: 20px;
}
.btn {
  padding: 12px 16px;
  border-radius: 12px;
  font-weight: 800;
  text-decoration: none;
  cursor: pointer;
  border: none;
}
.btn.primary {
  background: #2563eb;
  color: white;
}
.btn.ghost {
  background: #e5e7eb;
  color: #0f172a;
}
.btn:hover {
  opacity: .9;
}
</style>
