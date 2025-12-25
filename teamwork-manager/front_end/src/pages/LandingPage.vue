<template>
  <div class="lp">

    <!-- ===== HEADER ===== -->
    <header class="lp-header">
      <div class="container header-inner">
        <div class="brand">TeamWork</div>

        <div class="header-actions">
          <router-link to="/login" class="btn btn-outline-primary">
            Đăng nhập
          </router-link>
          <router-link to="/register" class="btn btn-primary">
            Đăng ký miễn phí
          </router-link>
        </div>
      </div>
    </header>

    <!-- ===== HERO ===== -->
    <section class="hero">
      <div class="container hero-inner">
        <div class="hero-text">
          <h1>
            Quản lý công việc nhóm <br />
            <span>đơn giản và hiệu quả</span>
          </h1>

          <p>
            TeamWork giúp bạn tạo nhóm, phân công nhiệm vụ,
            theo dõi tiến độ, chat nhóm và đánh giá đóng góp
            minh bạch như Trello.
          </p>

          <div class="hero-actions">
            <router-link to="/register" class="btn btn-primary btn-lg">
              Bắt đầu miễn phí
            </router-link>
            <router-link to="/login" class="btn btn-outline-secondary btn-lg">
              Đã có tài khoản
            </router-link>
          </div>
        </div>

        <div class="hero-board">
  <video
  class="hero-video"
  autoplay
  muted
  loop
  playsinline
>
  <source src="@/assets/hello.mp4" type="video/mp4" />
  Trình duyệt của bạn không hỗ trợ video.
</video>

</div>
        </div>
    </section>

    <!-- ===== FEATURES ===== -->
    <section class="features">
      <div class="container">
        <h2 class="section-title">
          Tất cả những gì nhóm bạn cần
        </h2>

        <div class="feature-grid">
          <div class="feature">
            📋
            <h5>Quản lý theo nhóm</h5>
            <p>
              Mỗi nhóm có nhiệm vụ, chat và bài nộp riêng biệt,
              tránh nhầm lẫn dữ liệu.
            </p>
          </div>

          <div class="feature">
            🧩
            <h5>Bảng Kanban</h5>
            <p>
              Theo dõi công việc theo trạng thái
              To do – Doing – Done.
            </p>
          </div>

          <div class="feature">
            💬
            <h5>Chat nhóm</h5>
            <p>
              Trao đổi công việc gần thời gian thực
              ngay trong nhóm.
            </p>
          </div>

          <div class="feature">
            🤖
            <h5>Trợ lý AI</h5>
            <p>
              Gợi ý ưu tiên công việc, nhắc deadline,
              phát hiện nhiệm vụ trễ hạn.
            </p>
          </div>

          <div class="feature">
            📄
            <h5>Nộp bài & PDF</h5>
            <p>
              Nộp bài nhiều phiên bản, xuất báo cáo PDF
              gửi giảng viên.
            </p>
          </div>

          <div class="feature">
            📊
            <h5>Đánh giá đóng góp</h5>
            <p>
              Thống kê và chấm điểm đóng góp
              công bằng, minh bạch.
            </p>
          </div>
        </div>
      </div>
    </section>

    <!-- ===== CTA ===== -->
    <section class="cta">
      <div class="container text-center">
        <h2>Sẵn sàng làm việc nhóm hiệu quả hơn?</h2>
        <p>
          Phù hợp cho sinh viên, nhóm dự án và giảng viên.
        </p>
        <router-link to="/register" class="btn btn-primary btn-lg">
          Đăng ký miễn phí
        </router-link>
      </div>
    </section>

    <!-- ===== FOOTER ===== -->
    <footer class="footer">
      <div class="container text-center">
        © 2025 TeamWork – Website Quản Lý Công Việc Nhóm
      </div>
    </footer>

  </div>
</template>

<style scoped>
/* ===== RESET ===== */
.lp {
  background: #f4f5f7;
}

/* ===== HEADER ===== */
.lp-header {
  background: white;
  border-bottom: 1px solid #e5e7eb;
}
.header-inner {
  display: flex;
  justify-content: space-between;
  align-items: center;
  height: 64px;
}
.brand {
  font-size: 22px;
  font-weight: bold;
  color: #0079bf;
}
.header-actions .btn {
  margin-left: 10px;
}

/* ===== HERO ===== */
.hero {
  padding: 80px 0;
}
.hero-inner {
  display: flex;
  gap: 50px;
  align-items: center;
}
.hero-text h1 {
  font-size: 42px;
  font-weight: bold;
}
.hero-text span {
  color: #0079bf;
}
.hero-text p {
  margin-top: 20px;
  font-size: 18px;
  color: #475569;
}
.hero-actions {
  margin-top: 30px;
}
.hero-actions .btn {
  margin-right: 15px;
}

/* ===== BOARD MOCK ===== */
.board {
  display: flex;
  gap: 15px;
  background: #026aa7;
  padding: 15px;
  border-radius: 10px;
}
.column {
  background: #ebecf0;
  padding: 10px;
  border-radius: 6px;
  width: 160px;
}
.column h6 {
  font-weight: bold;
}
.card {
  background: white;
  padding: 8px;
  border-radius: 4px;
  margin-top: 8px;
  font-size: 14px;
}
.card.done {
  text-decoration: line-through;
  opacity: 0.7;
}

/* ===== FEATURES ===== */
.features {
  background: white;
  padding: 80px 0;
}
.section-title {
  text-align: center;
  margin-bottom: 50px;
}
.feature-grid {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 30px;
}
.feature {
  background: #f8fafc;
  padding: 25px;
  border-radius: 10px;
}
.feature h5 {
  margin-top: 10px;
  font-weight: 600;
}

/* ===== CTA ===== */
.cta {
  background: #e0f2fe;
  padding: 70px 0;
}

/* ===== FOOTER ===== */
.footer {
  background: #026aa7;
  color: white;
  padding: 20px 0;
}

/* ===== HERO VIDEO ===== */
.hero-video {
  width: 420px;
  max-width: 100%;
  border-radius: 12px;
  box-shadow: 0 20px 40px rgba(0,0,0,0.15);
  background: black;
  transform: rotate(-1.5deg);
}

</style>
