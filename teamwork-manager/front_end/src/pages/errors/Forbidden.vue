<template>
  <div class="forbidden">
    <div class="card">
      <div class="icon">🚫</div>

      <h1>403</h1>
      <h2>Truy cập bị từ chối</h2>

      <p>
        Bạn không có quyền truy cập chức năng hoặc tài nguyên này.
      </p>

      <div class="actions">
        <router-link
          to="/dashboard"
          class="btn primary"
        >
          ⬅️ Về Dashboard
        </router-link>

        <button
          class="btn ghost"
          @click="goBack"
        >
          ↩️ Quay lại
        </button>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'ForbiddenPage',
  methods: {
    goBack() {
      this.$router.back()
    },
  },
}
</script>

<style scoped>
.forbidden {
  min-height: 100vh;
  background: linear-gradient(135deg, #eef2ff, #f8fafc);
  display: flex;
  justify-content: center;
  align-items: center;
}

/* CARD */
.card {
  background: white;
  padding: 40px;
  border-radius: 18px;
  width: 420px;
  text-align: center;
  box-shadow: 0 30px 60px rgba(0,0,0,.12);
  animation: fadeUp .4s ease;
}

@keyframes fadeUp {
  from {
    opacity: 0;
    transform: translateY(20px) scale(.96);
  }
  to {
    opacity: 1;
    transform: none;
  }
}

/* ICON */
.icon {
  font-size: 64px;
  margin-bottom: 12px;
}

/* TEXT */
h1 {
  font-size: 56px;
  margin: 0;
  color: #dc2626;
}
h2 {
  font-size: 22px;
  margin: 10px 0;
  font-weight: 800;
}
p {
  color: #64748b;
  margin-bottom: 26px;
}

/* BUTTON */
.actions {
  display: flex;
  justify-content: center;
  gap: 12px;
}
.btn {
  padding: 12px 18px;
  border-radius: 12px;
  font-weight: 700;
  cursor: pointer;
  text-decoration: none;
}
.btn.primary {
  background: #2563eb;
  color: white;
}
.btn.ghost {
  background: #e5e7eb;
  border: none;
}
.btn:hover {
  transform: translateY(-2px);
}
</style>
