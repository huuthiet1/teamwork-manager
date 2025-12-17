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

// Lấy danh sách nhóm mà user tham gia
$stmt = $conn->prepare("
    SELECT g.id, g.name, g.description, g.join_code, g.leader_id,
           (CASE WHEN g.leader_id = ? THEN 'leader' ELSE 'member' END) as role
    FROM group_members gm
    JOIN group_list g ON gm.group_id = g.id
    WHERE gm.user_id = ?
");
$stmt->execute([$user_id, $user_id]);
$groups = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Danh sách nhóm</title>

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Icons -->
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
        .sidebar a:hover {
            background: #374151;
            color: white;
        }
        .sidebar .active {
            background: #111827;
            font-weight: bold;
        }
        .main { margin-left: 240px; padding: 25px; }
        .group-card {
            border-radius: 12px;
            padding: 20px;
            border: 1px solid #ddd;
            transition: 0.3s;
            background: white;
        }
        .group-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 20px rgba(0,0,0,0.1);
        }
    </style>
</head>

<body>

<!-- SIDEBAR -->
<div class="sidebar">
    <h3 class="text-center mb-4">⚡ WebNhóm</h3>

    <a href="dashboard.php"><i class="fa fa-home"></i> Dashboard</a>
    <a href="groups.php" class="active"><i class="fa fa-users"></i> Nhóm của tôi</a>
    <a href="tasks.php"><i class="fa fa-tasks"></i> Nhiệm vụ</a>
    <a href="chat.php"><i class="fa fa-comments"></i> Chat nhóm</a>
    <a href="profile.php"><i class="fa fa-user"></i> Hồ sơ</a>
    <a href="../logout.php" class="text-danger"><i class="fa fa-sign-out-alt"></i> Đăng xuất</a>
</div>

<!-- MAIN CONTENT -->
<div class="main">
    <h2 class="fw-bold">Nhóm của tôi</h2>
    <p class="text-muted">Quản lý tất cả các nhóm bạn đang tham gia</p>

    <!-- Nút tạo nhóm -->
    <div class="mb-3">
        <a href="create_group.php" class="btn btn-primary">
            <i class="fa fa-plus"></i> Tạo nhóm mới
        </a>
        <a href="join_group.php" class="btn btn-success">
            <i class="fa fa-key"></i> Tham gia nhóm (mã 6 số)
        </a>
    </div>

    <div class="row g-4">
        <?php if (count($groups) > 0): ?>
            <?php foreach ($groups as $g): ?>
                <div class="col-md-4">
                    <div class="group-card shadow-sm">
                        <h4 class="fw-bold"><?php echo $g["name"]; ?></h4>
                        <p><?php echo $g["description"]; ?></p>

                        <p>
                            <strong>Mã tham gia:</strong> 
                            <span class="text-primary"><?php echo $g["join_code"]; ?></span>
                        </p>

                        <p>
                            <strong>Vai trò:</strong>
                            <?php if ($g["role"] == "leader"): ?>
                                <span class="badge bg-danger">Leader</span>
                            <?php else: ?>
                                <span class="badge bg-secondary">Member</span>
                            <?php endif; ?>
                        </p>

                        <a href="group_detail.php?id=<?php echo $g["id"]; ?>" class="btn btn-outline-primary w-100">
                            <i class="fa fa-folder-open"></i> Vào nhóm
                        </a>
                    </div>
                </div>
            <?php endforeach; ?>

        <?php else: ?>
            <p class="text-muted">Bạn chưa tham gia nhóm nào.</p>
        <?php endif; ?>
    </div>
</div>

</body>
</html>
