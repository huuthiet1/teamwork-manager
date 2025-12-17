<?php
require_once "../../controllers/check_login.php";
require_once "../../config/Database.php";

$db   = new Database();
$conn = $db->connect();

$user_id  = $_SESSION["user_id"];
$group_id = $_GET["id"] ?? null;

if (!$group_id) {
    header("Location: ../dashboard/index.php");
    exit;
}

/* Kiểm tra user có thuộc nhóm không */
$check = $conn->prepare("
    SELECT gm.role, g.*
    FROM group_members gm
    JOIN group_list g ON gm.group_id = g.id
    WHERE gm.user_id = :uid AND gm.group_id = :gid
");
$check->execute([
    "uid" => $user_id,
    "gid" => $group_id
]);
$group = $check->fetch(PDO::FETCH_ASSOC);

if (!$group) {
    header("Location: ../dashboard/index.php");
    exit;
}

$isLeader = ($group["role"] === "leader");

/* Lấy danh sách thành viên */
$members = $conn->prepare("
    SELECT u.name, u.email, gm.role
    FROM group_members gm
    JOIN users u ON gm.user_id = u.id
    WHERE gm.group_id = :gid
");
$members->execute(["gid" => $group_id]);
$members = $members->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Chi tiết nhóm</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">

    <style>
        body { background:#f4f6f9; }
        .card { border-radius:16px; }
        .badge-role { font-size: 13px; }
    </style>
</head>
<body>

<!-- NAVBAR -->
<nav class="navbar navbar-dark bg-dark px-4">
    <a href="../dashboard/index.php" class="navbar-brand">
        ⬅ Bảng điều khiển
    </a>
    <span class="text-light">
        👥 <?= htmlspecialchars($group["name"]) ?>
    </span>
</nav>

<div class="container mt-4">

    <!-- THÔNG TIN NHÓM -->
    <div class="card mb-4 shadow">
        <div class="card-body">
            <h4 class="fw-bold"><?= htmlspecialchars($group["name"]) ?></h4>
            <p class="text-muted"><?= htmlspecialchars($group["description"]) ?></p>

            <div class="row">
                <div class="col-md-4">
                    <strong>Deadline nhóm:</strong><br>
                    <?= $group["deadline"] ? $group["deadline"] : "Không có" ?>
                </div>

                <?php if ($isLeader): ?>
                <div class="col-md-4">
                    <strong>Mã tham gia nhóm (OTP):</strong><br>
                    <span class="badge bg-primary fs-6">
                        <?= $group["join_code"] ?>
                    </span>
                </div>
                <?php endif; ?>

                <div class="col-md-4">
                    <strong>Vai trò của bạn:</strong><br>
                    <span class="badge bg-info">
                        <?= $group["role"] === "leader" ? "Trưởng nhóm" : "Thành viên" ?>
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- THÀNH VIÊN -->
    <div class="card shadow mb-4">
        <div class="card-body">
            <h5 class="fw-bold mb-3">👤 Thành viên trong nhóm</h5>

            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Họ tên</th>
                        <th>Email</th>
                        <th>Vai trò</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($members as $i => $m): ?>
                        <tr>
                            <td><?= $i + 1 ?></td>
                            <td><?= htmlspecialchars($m["name"]) ?></td>
                            <td><?= htmlspecialchars($m["email"]) ?></td>
                            <td>
                                <?php if ($m["role"] === "leader"): ?>
                                    <span class="badge bg-danger badge-role">Trưởng nhóm</span>
                                <?php else: ?>
                                    <span class="badge bg-secondary badge-role">Thành viên</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- HÀNH ĐỘNG -->
    <div class="card shadow">
        <div class="card-body">
            <h5 class="fw-bold mb-3">⚙️ Chức năng nhóm</h5>

            <a href="../tasks/create.php?group_id=<?= $group_id ?>" class="btn btn-primary me-2">
                + Giao nhiệm vụ
            </a>

            <a href="../chat/index.php?group_id=<?= $group_id ?>" class="btn btn-success me-2">
                💬 Chat nhóm
            </a>

            <a href="#" class="btn btn-warning">
                📄 Xuất báo cáo PDF
            </a>
        </div>
    </div>

</div>

</body>
</html>
