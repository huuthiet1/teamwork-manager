<?php
session_start();

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit;
}

require_once "../config/database.php";
$db = new Database();
$conn = $db->connect();

$group_id = $_GET["group"] ?? 0;
$user_id  = $_SESSION["user_id"];

// Kiểm tra nhóm có tồn tại
$stmt = $conn->prepare("SELECT * FROM group_list WHERE id = ?");
$stmt->execute([$group_id]);
$group = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$group) {
    die("❌ Nhóm không tồn tại!");
}

// Kiểm tra user có phải leader hay không
$is_leader = ($group["leader_id"] == $user_id);
if (!$is_leader) {
    die("⚠️ Bạn không phải leader nên không được giao nhiệm vụ.");
}

// Lấy danh sách thành viên nhóm
$members = $conn->prepare("
    SELECT u.id, u.name 
    FROM group_members gm
    JOIN users u ON gm.user_id = u.id
    WHERE gm.group_id = ?
");
$members->execute([$group_id]);
$memberList = $members->fetchAll(PDO::FETCH_ASSOC);

// Khi leader gửi nhiệm vụ
$message = "";
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $assigned_to = $_POST["assign_to"];
    $name       = trim($_POST["name"]);
    $desc       = trim($_POST["description"]);
    $difficulty = $_POST["difficulty"];
    $deadline   = $_POST["deadline"];

    $stmt = $conn->prepare("
        INSERT INTO tasks (group_id, assigned_to, created_by, name, description, difficulty, deadline)
        VALUES (?, ?, ?, ?, ?, ?, ?)
    ");

    $stmt->execute([
        $group_id,
        $assigned_to,
        $user_id,
        $name,
        $desc,
        $difficulty,
        $deadline
    ]);

    // Chuyển về trang chi tiết nhóm
    header("Location: group_detail.php?id=$group_id");
    exit;
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<title>Giao nhiệm vụ</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">

<style>
    body { background: #f0f2f5; }
    .sidebar {
        width: 240px; height: 100vh;
        background: #1f2937; position: fixed;
        left: 0; top: 0; padding-top: 20px;
    }
    .sidebar a { display: block; padding: 12px 20px; color: #e5e7eb; }
    .sidebar a:hover { background: #374151; }
    .main { margin-left: 240px; padding: 25px; }
    .task-card {
        max-width: 650px; background: white;
        padding: 25px; border-radius: 15px;
        border: 1px solid #ddd;
    }
</style>
</head>

<body>

<!-- SIDEBAR -->
<div class="sidebar">
    <h3 class="text-center mb-4 text-white">⚡ WebNhóm</h3>

    <a href="dashboard.php"><i class="fa fa-home"></i> Dashboard</a>
    <a href="groups.php"><i class="fa fa-users"></i> Nhóm của tôi</a>
    <a href="group_detail.php?id=<?php echo $group_id; ?>"><i class="fa fa-list"></i> Chi tiết nhóm</a>
    <a class="active bg-dark text-white"><i class="fa fa-plus"></i> Giao nhiệm vụ</a>
    <a href="tasks.php"><i class="fa fa-tasks"></i> Nhiệm vụ</a>
    <a href="../logout.php" class="text-danger"><i class="fa fa-sign-out-alt"></i> Đăng xuất</a>
</div>

<!-- MAIN CONTENT -->
<div class="main">
    <h2 class="fw-bold">Giao nhiệm vụ</h2>
    <p class="text-muted">Tạo nhiệm vụ mới cho nhóm <strong><?php echo $group["name"]; ?></strong></p>

    <div class="task-card shadow">
        <form method="POST">

            <div class="mb-3">
                <label class="form-label">Tên nhiệm vụ</label>
                <input type="text" name="name" class="form-control" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Mô tả</label>
                <textarea name="description" rows="3" class="form-control"></textarea>
            </div>

            <div class="mb-3">
                <label class="form-label">Giao cho</label>
                <select name="assign_to" class="form-select" required>
                    <?php foreach ($memberList as $m): ?>
                        <option value="<?php echo $m["id"]; ?>">
                            <?php echo $m["name"]; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label">Độ khó</label>
                <select name="difficulty" class="form-select" required>
                    <option value="1">⭐ Dễ</option>
                    <option value="2">⭐⭐ Khá</option>
                    <option value="3">⭐⭐⭐ Trung bình</option>
                    <option value="4">⭐⭐⭐⭐ Khó</option>
                    <option value="5">⭐⭐⭐⭐⭐ Rất khó</option>
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label">Deadline</label>
                <input type="datetime-local" name="deadline" class="form-control" required>
            </div>

            <button class="btn btn-primary w-100">
                <i class="fa fa-paper-plane"></i> Giao nhiệm vụ
            </button>

        </form>
    </div>
</div>

</body>
</html>
