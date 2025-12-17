<?php
session_start();

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit;
}

require_once "../config/database.php";

$db = new Database();
$conn = $db->connect();

$user_id = $_SESSION["user_id"];

// Lấy danh sách nhiệm vụ user được giao
$stmt = $conn->prepare("
    SELECT t.*, g.name AS group_name
    FROM tasks t
    JOIN group_list g ON t.group_id = g.id
    WHERE t.assigned_to = ?
    ORDER BY t.deadline ASC
");
$stmt->execute([$user_id]);
$tasks = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<title>Nhiệm vụ của tôi</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">

<style>
    body { background: #f0f2f5; }
    .sidebar {
        width: 240px; height: 100vh;
        background: #1f2937; position: fixed;
        left: 0; top: 0; padding-top: 20px;
    }
    .sidebar a {
        display: block;
        padding: 12px 20px;
        color: #e5e7eb; text-decoration: none;
    }
    .sidebar a:hover { background: #374151; }
    .sidebar .active { background: #111827; }
    .main { margin-left: 240px; padding: 25px; }
    .task-card {
        background: white; padding: 20px;
        border: 1px solid #ddd; border-radius: 10px;
        transition: 0.3s;
    }
    .task-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 10px 20px rgba(0,0,0,0.08);
    }
</style>
</head>

<body>

<!-- SIDEBAR -->
<div class="sidebar">
    <h3 class="text-center text-white mb-4">⚡ WebNhóm</h3>

    <a href="dashboard.php"><i class="fa fa-home"></i> Dashboard</a>
    <a href="groups.php"><i class="fa fa-users"></i> Nhóm của tôi</a>
    <a href="tasks.php" class="active"><i class="fa fa-tasks"></i> Nhiệm vụ</a>
    <a href="profile.php"><i class="fa fa-user"></i> Hồ sơ</a>
    <a href="../logout.php" class="text-danger"><i class="fa fa-sign-out-alt"></i> Đăng xuất</a>
</div>

<!-- MAIN CONTENT -->
<div class="main">

    <h2 class="fw-bold">Nhiệm vụ của tôi</h2>
    <p class="text-muted">Tất cả nhiệm vụ được giao cho bạn</p>

    <div class="row g-4 mt-3">
        <?php if (count($tasks) == 0): ?>
            <p class="text-muted">Bạn chưa được giao nhiệm vụ nào.</p>
        <?php endif; ?>

        <?php foreach ($tasks as $t): ?>
            <div class="col-md-6">
                <div class="task-card shadow-sm">

                    <h4 class="fw-bold"><?php echo $t["name"]; ?></h4>
                    <p><?php echo $t["description"]; ?></p>

                    <p>
                        <strong>Nhóm:</strong> 
                        <span class="text-primary"><?php echo $t["group_name"]; ?></span>
                    </p>

                    <p>
                        <strong>Deadline:</strong>
                        <?php echo $t["deadline"]; ?>
                    </p>

                    <p>
                        <strong>Độ khó:</strong>
                        <span class="badge bg-dark"><?php echo $t["difficulty"]; ?>/5</span>
                    </p>

                    <p>
                        <strong>Trạng thái:</strong>
                        <?php
                            $status_class = "secondary";
                            if ($t["status"] == "doing") $status_class = "warning";
                            if ($t["status"] == "done")  $status_class = "success";
                            if ($t["status"] == "late")  $status_class = "danger";
                        ?>
                        <span class="badge bg-<?php echo $status_class; ?>">
                            <?php echo $t["status"]; ?>
                        </span>
                    </p>

                    <a href="task_detail.php?id=<?php echo $t["id"]; ?>" 
                       class="btn btn-outline-primary w-100">
                        <i class="fa fa-eye"></i> Xem chi tiết
                    </a>

                </div>
            </div>
        <?php endforeach; ?>
    </div>

</div>

</body>
</html>
