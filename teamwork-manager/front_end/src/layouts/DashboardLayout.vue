<template>
  <div class="tw-app">
    <!-- ===== TOP BAR ===== -->
    <header class="topbar">
      <div class="left">
        <router-link to="/dashboard" class="brand">
          <span class="brand-dot"></span>
          <span class="brand-text">TeamWork</span>
        </router-link>
      </div>
    </header>

    <!-- ===== BODY ===== -->
    <div class="body">
      <!-- SIDEBAR -->
      <aside class="sidebar">
        <div>
          <div class="sb-title">Workspace</div>

          <nav class="sb-nav">
            <!-- DASHBOARD -->
            <router-link to="/dashboard" class="sb-item">
              <span class="icon">📋</span>
              <span>Tổng quan</span>
            </router-link>

            <!-- GROUP -->
            <router-link to="/groups" class="sb-item">
              <span class="icon">👥</span>
              <span>Nhóm làm việc</span>
            </router-link>

            <!-- TASK -->
            <router-link to="/tasks" class="sb-item">
              <span class="icon">✅</span>
              <span>Nhiệm vụ</span>
            </router-link>

            <!-- CHAT -->
            <router-link to="/chat" class="sb-item">
              <span class="icon">💬</span>
              <span>Chat nhóm</span>
            </router-link>

            <!-- NOTIFICATION -->
            <router-link to="/notifications" class="sb-item">
              <span class="icon">🔔</span>
              <span>Thông báo</span>
            </router-link>

            <!-- AI ASSISTANT -->
            <router-link to="/ai" class="sb-item">
              <span class="icon">🤖</span>
              <span>Trợ lý AI</span>
            </router-link>

            <!-- REPORT -->
            <router-link to="/reports" class="sb-item">
              <span class="icon">📄</span>
              <span>Báo cáo</span>
            </router-link>

            <!-- CONTRIBUTION -->
            <router-link to="/contributions" class="sb-item">
              <span class="icon">📊</span>
              <span>Đóng góp</span>
            </router-link>

            <!-- PROFILE -->
            <router-link to="/profile" class="sb-item">
              <span class="icon">👤</span>
              <span>Hồ sơ cá nhân</span>
            </router-link>
          </nav>
        </div>

        <!-- FOOTER -->
        <div class="sb-footer">
          <div class="hint">
            💡 Vui lòng chọn nhóm trước khi vào Nhiệm vụ, Chat hoặc Báo cáo
          </div>

          <button class="logout" @click="logout">
            Đăng xuất
          </button>
        </div>
      </aside>

      <!-- MAIN CONTENT -->
      <main class="content">
        <div class="page-enter">
          <router-view />
        </div>
      </main>
    </div>
  </div>
</template>

<script>
export default {
  methods: {
    logout() {
      localStorage.clear()
      this.$router.push('/login')
    },
  },
}
</script>

<style scoped>
/* =====================================================
   THEME – BLUE MOTION SAAS
===================================================== */
:root {
  --blue-main: #1d4ed8;
  --blue-dark: #1e40af;
  --blue-soft: #e0ecff;
  --blue-hover: #dbeafe;
  --blue-border: #bfdbfe;

  --text-main: #0f172a;
  --text-sub: #334155;

  --bg-app: #eff6ff;
  --bg-white: #ffffff;
}

/* =====================================================
   ROOT
===================================================== */
.tw-app {
  height: 100vh;
  display: flex;
  flex-direction: column;
  background: var(--bg-app);
  font-family: Inter, system-ui, sans-serif;
  color: var(--text-main);
}

/* =====================================================
   TOPBAR – FADE + SLIDE
===================================================== */
.topbar {
  height: 56px;
  background: linear-gradient(180deg, #ffffff, #f0f7ff);
  border-bottom: 1px solid var(--blue-border);
  display: flex;
  align-items: center;
  padding: 0 20px;
  box-shadow: 0 8px 24px rgba(29,78,216,.15);
  animation: topIn .5s ease;
}

@keyframes topIn {
  from { opacity: 0; transform: translateY(-12px); }
  to { opacity: 1; transform: none; }
}

.brand {
  display: flex;
  align-items: center;
  gap: 10px;
  text-decoration: none;
}

.brand-dot {
  width: 10px;
  height: 10px;
  border-radius: 50%;
  background: linear-gradient(135deg,var(--blue-main),#60a5fa);
  box-shadow: 0 0 0 4px rgba(29,78,216,.25);
  animation: pulse 2.2s infinite;
}

@keyframes pulse {
  0% { box-shadow: 0 0 0 0 rgba(29,78,216,.35); }
  70% { box-shadow: 0 0 0 8px rgba(29,78,216,0); }
  100% { box-shadow: 0 0 0 0 rgba(29,78,216,0); }
}

.brand-text {
  font-size: 18px;
  font-weight: 900;
  color: var(--blue-main);
}

/* =====================================================
   BODY
===================================================== */
.body {
  flex: 1;
  display: flex;
  min-height: 0;
}

/* =====================================================
   SIDEBAR – SLIDE IN
===================================================== */
.sidebar {
  width: 260px;
  background: linear-gradient(180deg,#e8f1ff,#f4f9ff);
  border-right: 1px solid var(--blue-border);
  padding: 16px 14px;
  display: flex;
  flex-direction: column;
  justify-content: space-between;
  animation: sidebarIn .45s cubic-bezier(.22,.61,.36,1);
}

@keyframes sidebarIn {
  from { transform: translateX(-20px); opacity: 0; }
  to { transform: none; opacity: 1; }
}

.sb-title {
  font-size: 11px;
  letter-spacing: .8px;
  color: #1e3a8a;
  font-weight: 900;
  margin-bottom: 14px;
  text-transform: uppercase;
}

/* =====================================================
   NAV ITEM – SEQUENTIAL ANIMATION
===================================================== */
.sb-nav .sb-item {
  opacity: 0;
  transform: translateX(-10px);
  animation: navIn .4s ease forwards;
}

.sb-nav .sb-item:nth-child(1){ animation-delay:.05s }
.sb-nav .sb-item:nth-child(2){ animation-delay:.1s }
.sb-nav .sb-item:nth-child(3){ animation-delay:.15s }
.sb-nav .sb-item:nth-child(4){ animation-delay:.2s }
.sb-nav .sb-item:nth-child(5){ animation-delay:.25s }

@keyframes navIn {
  to { opacity:1; transform:none }
}

/* =====================================================
   SIDEBAR ITEM – HOVER / ACTIVE
===================================================== */
.sb-item {
  position: relative;
  display: flex;
  align-items: center;
  gap: 14px;
  padding: 12px 14px;
  border-radius: 16px;
  text-decoration: none;
  color: #1e293b;
  font-weight: 700;
  transition: all .25s ease;
}

.sb-item:hover {
  background: var(--blue-hover);
  color: var(--blue-main);
  transform: translateX(8px) scale(1.02);
  box-shadow: 0 12px 30px rgba(29,78,216,.28);
}

.sb-item:active {
  transform: translateX(8px) scale(.96);
}

.sb-item.router-link-active {
  background: #e0ecff;      /* xanh nhạt */
  color: #1d4ed8;           /* xanh đậm */
  font-weight: 800;

}

.sb-item.router-link-active::before {
  content:'';
  position:absolute;
  left:6px;
  top:10px;
  bottom:10px;
  width:4px;
  background:#fff;
  border-radius:4px;
}

/* icon micro motion */
.sb-item .icon {
  transition: transform .3s ease;
}
.sb-item:hover .icon {
  transform: scale(1.2) rotate(-4deg);
}

/* =====================================================
   FOOTER
===================================================== */
.sb-footer {
  display: flex;
  flex-direction: column;
  gap: 14px;
}

.hint {
  font-size: 12px;
  line-height: 1.5;
  color: #1e3a8a;
  background: #e0ecff;
  border: 1px dashed var(--blue-border);
  padding: 12px;
  border-radius: 14px;
  animation: fadeUp .5s ease;
}

@keyframes fadeUp {
  from { opacity:0; transform:translateY(10px); }
  to { opacity:1; transform:none; }
}

/* =====================================================
   LOGOUT
===================================================== */
.logout {
  background: linear-gradient(135deg,var(--blue-main),var(--blue-dark));
  border: none;
  color: #fff;
  padding: 12px;
  border-radius: 16px;
  cursor: pointer;
  font-weight: 900;
  transition: all .25s ease;
}

.logout:hover {
  transform: translateY(-3px);
  box-shadow: 0 16px 36px rgba(29,78,216,.6);
}

/* =====================================================
   CONTENT – OPEN / CLOSE EFFECT
===================================================== */
.content {
  flex: 1;
  padding: 28px;
  overflow: auto;
  background: #f8fbff;
}

.page-enter {
  animation: pageIn .45s cubic-bezier(.22,.61,.36,1);
}

@keyframes pageIn {
  from { opacity:0; transform:scale(.97) translateY(16px); }
  to { opacity:1; transform:none; }
}

/* =====================================================
   SCROLLBAR
===================================================== */
.content::-webkit-scrollbar {
  width: 8px;
}
.content::-webkit-scrollbar-thumb {
  background: #93c5fd;
  border-radius: 8px;
}
.content::-webkit-scrollbar-thumb:hover {
  background: #60a5fa;
}
</style>
