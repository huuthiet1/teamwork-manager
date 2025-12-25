<template>
  <div class="dashboard">

    <!-- HEADER -->
    <div class="header">
      <h1>Bảng công việc</h1>
      <p>Tổng quan tất cả nhóm & nhiệm vụ của bạn</p>
    </div>

    <!-- STATS -->
    <div class="stats">
      <div class="stat blue">
        <strong>3</strong>
        <span>Nhóm</span>
      </div>
      <div class="stat green">
        <strong>12</strong>
        <span>Nhiệm vụ</span>
      </div>
      <div class="stat orange">
        <strong>4</strong>
        <span>Đang làm</span>
      </div>
      <div class="stat red">
        <strong>1</strong>
        <span>Trễ hạn</span>
      </div>
    </div>

    <!-- TASK GRID -->
    <div class="grid">
      <div class="card">
        <h3>🔔 Việc cần làm hôm nay</h3>

        <div class="task">
          <div>
            <strong>Thiết kế giao diện</strong>
            <small>Nhóm Web · còn 6 giờ</small>
          </div>
          <span class="badge doing">Đang làm</span>
        </div>

        <div class="task">
          <div>
            <strong>Viết báo cáo chương 3</strong>
            <small>Nhóm AI · còn 20 giờ</small>
          </div>
          <span class="badge doing">Đang làm</span>
        </div>
      </div>

      <div class="card danger">
        <h3>⚠️ Nhiệm vụ trễ hạn</h3>

        <div class="task">
          <div>
            <strong>Xây dựng API</strong>
            <small>Nhóm Web · trễ 1 ngày</small>
          </div>
          <span class="badge late">Trễ hạn</span>
        </div>
      </div>
    </div>

    <!-- GROUPS -->
    <div class="card">
      <h3>👥 Nhóm của bạn</h3>

      <div class="groups">
        <div class="group">
          <div>
            <strong>Nhóm Web</strong>
            <small>Đồ án cuối kỳ</small>
          </div>
          <button>Mở bảng</button>
        </div>

        <div class="group">
          <div>
            <strong>Nhóm AI</strong>
            <small>Báo cáo môn học</small>
          </div>
          <button>Mở bảng</button>
        </div>
      </div>
    </div>

    <!-- AI -->
    <div class="card ai">
      <h3>🤖 Trợ lý AI</h3>
      <ul>
        <li>Ưu tiên nhiệm vụ trễ hạn “Xây dựng API”</li>
        <li>Nhiệm vụ “Thiết kế giao diện” sắp đến hạn</li>
      </ul>
    </div>

  </div>
</template>

<script>
export default {
  name: 'DashboardPage',
}
</script>

<style scoped>
.dashboard {
  max-width: 1200px;
  margin: 0 auto;
  width: 100%;
}

/* HEADER */
.header {
  margin-bottom: 24px;
}
.header h1 {
  font-size: 26px;
  font-weight: 800;
}
.header p {
  color: #64748b;
}

/* STATS */
.stats {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
  gap: 20px;
  margin-bottom: 28px;
}
.stat {
  padding: 20px;
  border-radius: 14px;
  color: white;
}
.stat strong {
  font-size: 28px;
}
.blue { background:#2563eb }
.green { background:#16a34a }
.orange { background:#f59e0b }
.red { background:#dc2626 }

/* GRID */
.grid {
  display: grid;
  grid-template-columns: minmax(0, 2fr) minmax(0, 1fr);
  gap: 20px;
  margin-bottom: 28px;
}

/* CARD */
.card {
  background: white;
  border-radius: 14px;
  padding: 20px;
}
.card.danger {
  border-left: 4px solid #dc2626;
}
.card.ai {
  background: #f8fafc;
}

/* TASK */
.task {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 12px 0;
  border-bottom: 1px solid #e5e7eb;
}
.task:last-child {
  border-bottom: none;
}
.task small {
  font-size: 13px;
  color: #64748b;
}

/* BADGE */
.badge {
  padding: 4px 10px;
  border-radius: 999px;
  font-size: 12px;
  color: white;
}
.doing { background:#2563eb }
.late { background:#dc2626 }

/* GROUPS */
.groups {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(260px,1fr));
  gap: 16px;
}
.group {
  border: 1px solid #e5e7eb;
  border-radius: 12px;
  padding: 16px;
  display: flex;
  justify-content: space-between;
  align-items: center;
}
.group button {
  background: #2563eb;
  color: white;
  border: none;
  padding: 8px 14px;
  border-radius: 10px;
  cursor: pointer;
}
</style>
