<?php
session_start();

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit;
}

require_once "../config/database.php";

$db = new Database();
$conn = $db->connect();

$message = "";
$message_type = "danger";  // default

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $code = trim($_POST["join_code"]);
    $user_id = $_SESSION["user_id"];

    // Kiểm tra mã nhóm có tồn tại không
    $stmt = $conn->prepare("SELECT * FROM group_list WHERE join_code = ?");
    $stmt->execute([$code]);
    $group = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$group) {
        $message = "❌ Mã nhóm không đúng!";
    } else {
        $group_id = $group["id"];

        // Kiểm tra user đã tham gia nhóm chưa
        $check = $conn->prepare("SELECT * FROM group_members WHERE group_id = ? AND user_id = ?");
        $check->execute([$group_id, $user_id]);

        if ($check->rowCount() > 0) {
            $message = "⚠️ Bạn đã tham gia nhóm này trước đó!";
            $message_type = "warning";
        } else {
            // Thêm vào group_members
            $insert = $conn->prepare("
                INSERT INTO group_members (group_id, user_id)
                VALUES (?, ?)
            ");
            $insert->execute([$group_id, $user_id]);

            $message = "🎉 Tham gia nhóm thành công!";
            $message_type = "success";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Tham gia nhóm</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">

    <style>
        body { background: #f0f2f5; }
        .sidebar {
            width: 240px;
            height: 100vh;
            background: #1f2937;
            position: fixed;
            top: 0; left: 0;
            color: #fff;
            padding-top: 20px;
        }
        .sidebar a {
            display: block;
            padding: 12px 20px;
            color: #e5e7eb;
            text-decoration: none;
        }
        .sidebar a:hover { background: #374151; }
        .sidebar .active { background: #111827; font-weight: bold; }
        .main { margin-left: 240px; padding: 25px; }
        .join-card {
            max-width: 500px;
            background: white;
            padding: 25px;
            border-radius: 12px;
            border: 1px solid #ddd;
        }
    </style>
</head>

<body>

<!-- SIDEBAR -->
<div class="sidebar">
    <h3 class="text-center mb-4">⚡ WebNhóm</h3>

    <a href="dashboard.php"><i class="fa fa-home"></i> Dashboard</a>
    <a href="groups.php"><i class="fa fa-users"></i> Nhóm của tôi</a>
    <a href="create_group.php"><i class="fa fa-plus"></i> Tạo nhóm</a>
    <a href="join_group.php" class="active"><i class="fa fa-key"></i> Tham gia nhóm</a>
    <a href="tasks.php"><i class="fa fa-tasks"></i> Nhiệm vụ</a>
    <a href="chat.php"><i class="fa fa-comments"></i> Chat nhóm</a>
    <a href="profile.php"><i class="fa fa-user"></i> Hồ sơ</a>
    <a href="../logout.php" class="text-danger"><i class="fa fa-sign-out-alt"></i> Đăng xuất</a>
</div>

<!-- MAIN CONTENT -->
<div class="main">
    <h2 class="fw-bold">Tham gia nhóm</h2>
    <p class="text-muted">Nhập mã nhóm 6 số do trưởng nhóm cung cấp</p>

    <?php if (!empty($message)): ?>
        <div class="alert alert-<?php echo $message_type; ?> mt-3">
            <?php echo $message; ?>
        </div>
    <?php endif; ?>

    <div class="join-card shadow-sm mt-3">
        <form method="POST">

            <div class="mb-3">
                <label class="form-label">Mã tham gia (6 số)</label>
                <input type="text" name="join_code" class="form-control" maxlength="6" required>
            </div>

            <button class="btn btn-primary w-100">
                <i class="fa fa-key"></i> Tham gia nhóm
            </button>

        </form>
    </div>

</div>

</body>
</html>
