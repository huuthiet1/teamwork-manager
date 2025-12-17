<?php
session_start();
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Web Quản Lý Công Việc Nhóm</title>

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">

    <style>
        body {
            background: #f4f6f9;
            font-family: Arial, sans-serif;
        }

        /* HERO */
        .hero {
            background: linear-gradient(135deg, #4c6ef5, #15aabf);
            color: #fff;
            padding: 120px 0;
            text-align: center;
        }
        .hero h1 {
            font-size: 52px;
            font-weight: 700;
        }
        .hero p {
            font-size: 18px;
            max-width: 700px;
            margin: 15px auto;
            opacity: .95;
        }

        /* STATS */
        .stat-box {
            background: #fff;
            border-radius: 15px;
            padding: 25px;
            text-align: center;
            box-shadow: 0 10px 25px rgba(0,0,0,.08);
        }
        .stat-box i {
            font-size: 36px;
            color: #4c6ef5;
        }
        .stat-box h3 {
            margin: 10px 0 0;
            font-weight: bold;
        }

        /* FEATURES */
        .feature {
            background: #fff;
            padding: 30px;
            border-radius: 15px;
            text-align: center;
            transition: .3s;
            height: 100%;
        }
        .feature:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 30px rgba(0,0,0,.1);
        }
        .feature i {
            font-size: 40px;
            color: #4c6ef5;
            margin-bottom: 15px;
        }

        /* STEPS */
        .step {
            text-align: center;
            padding: 20px;
        }
        .step span {
            display: inline-block;
            width: 45px;
            height: 45px;
            background: #4c6ef5;
            color: white;
            border-radius: 50%;
            line-height: 45px;
            font-weight: bold;
            margin-bottom: 10px;
        }

        footer {
            background: #212529;
            color: #ccc;
            padding: 15px;
            text-align: center;
            margin-top: 50px;
        }
    </style>
</head>

<body>

<!-- NAVBAR -->
<nav class="navbar navbar-expand-lg navbar-dark bg-dark px-4">
    <a class="navbar-brand fw-bold" href="index.php">Đừng làm 1 mình</a>

    <div class="ms-auto">
        <?php if (!isset($_SESSION["user_id"])): ?>
            <a href="pages/auth/login.php" class="btn btn-outline-light me-2">Đăng nhập</a>
            <a href="pages/auth/register.php" class="btn btn-warning">Đăng ký</a>
        <?php else: ?>
            <span class="text-light me-3">
                Xin chào, <?= htmlspecialchars($_SESSION["name"]) ?>
            </span>
            <a href="pages/dashboard/index.php" class="btn btn-success me-2">Dashboard</a>
            <a href="pages/auth/logout.php" class="btn btn-danger">Đăng xuất</a>
        <?php endif; ?>
    </div>
</nav>

<!-- HERO -->
<section class="hero">
    <h1>Quản Lý Công Việc Nhóm Thông Minh</h1>
    <p>
        Phân chia nhiệm vụ – Theo dõi tiến độ – Chấm điểm đóng góp minh bạch.
        Giải quyết triệt để tình trạng “một người làm, cả nhóm hưởng”.
    </p>

    <?php if (!isset($_SESSION["user_id"])): ?>
        <a href="pages/auth/register.php" class="btn btn-light btn-lg mt-3 px-5">
            🚀 Bắt đầu ngay
        </a>
    <?php else: ?>
        <a href="pages/dashboard/index.php" class="btn btn-light btn-lg mt-3 px-5">
            📊 Bắt đầu
        </a>
    <?php endif; ?>
</section>

<!-- STATS -->
<div class="container mt-n5">
    <div class="row g-4">
        <div class="col-md-3">
            <div class="stat-box">
                <i class="fa fa-users"></i>
                <h3>120+</h3>
                <p>Nhóm đã tạo</p>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-box">
                <i class="fa fa-tasks"></i>
                <h3>1,450+</h3>
                <p>Nhiệm vụ</p>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-box">
                <i class="fa fa-upload"></i>
                <h3>3,200+</h3>
                <p>Bài nộp</p>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-box">
                <i class="fa fa-file-pdf"></i>
                <h3>400+</h3>
                <p>Báo cáo PDF</p>
            </div>
        </div>
    </div>
</div>

<!-- HOW IT WORKS -->
<div class="container my-5">
    <h2 class="text-center fw-bold mb-4">Cách hoạt động</h2>
    <div class="row">
        <div class="col-md-4 step">
            <span>1</span>
            <h5>Tạo / Tham gia nhóm</h5>
            <p>Leader tạo nhóm, thành viên tham gia bằng mã OTP.</p>
        </div>
        <div class="col-md-4 step">
            <span>2</span>
            <h5>Giao & làm nhiệm vụ</h5>
            <p>Phân công rõ ràng, deadline cụ thể, theo dõi realtime.</p>
        </div>
        <div class="col-md-4 step">
            <span>3</span>
            <h5>Đánh giá & báo cáo</h5>
            <p>Tự động chấm điểm, xuất báo cáo PDF cho giảng viên.</p>
        </div>
    </div>
</div>

<!-- FEATURES -->
<div class="container my-5">
    <h2 class="text-center fw-bold mb-4">Tính năng nổi bật</h2>

    <div class="row g-4">
        <div class="col-md-4">
            <div class="feature">
                <i class="fa fa-users"></i>
                <h5>Quản lý nhóm</h5>
                <p>Tạo nhóm, phân quyền leader / member rõ ràng.</p>
            </div>
        </div>

        <div class="col-md-4">
            <div class="feature">
                <i class="fa fa-tasks"></i>
                <h5>Quản lý nhiệm vụ</h5>
                <p>Deadline, độ khó, trạng thái, lịch sử thay đổi.</p>
            </div>
        </div>

        <div class="col-md-4">
            <div class="feature">
                <i class="fa fa-comments"></i>
                <h5>Chat nhóm</h5>
                <p>Trao đổi realtime, gửi file, hình ảnh, audio.</p>
            </div>
        </div>

        <div class="col-md-4">
            <div class="feature">
                <i class="fa fa-upload"></i>
                <h5>Nộp bài minh chứng</h5>
                <p>Upload file, hình, video, ghi âm.</p>
            </div>
        </div>

        <div class="col-md-4">
            <div class="feature">
                <i class="fa fa-chart-line"></i>
                <h5>Chấm điểm đóng góp</h5>
                <p>Tự động tính % đóng góp từng thành viên.</p>
            </div>
        </div>

        <div class="col-md-4">
            <div class="feature">
                <i class="fa fa-file-pdf"></i>
                <h5>Báo cáo PDF</h5>
                <p>1 file duy nhất – giáo viên xem là hiểu ngay.</p>
            </div>
        </div>
    </div>
</div>

<footer>
    © 2025 Web Quản Lý Công Việc Nhóm — Design by Truong Huu Thiet
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
