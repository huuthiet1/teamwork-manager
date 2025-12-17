<?php
session_start();
if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit;
}

require_once "../config/database.php";
$db = new Database();
$conn = $db->connect();

$group_id = $_GET["id"] ?? 0;
$user_id  = $_SESSION["user_id"];

// Lấy thông tin nhóm
$stmt = $conn->prepare("SELECT * FROM group_list WHERE id = ?");
$stmt->execute([$group_id]);
$group = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$group) {
    die("Nhóm không tồn tại");
}

// Lấy danh sách thành viên
$members = $conn->prepare("
    SELECT u.id, u.name, u.email, 
           IF(g.leader_id = u.id, 'Leader', 'Member') AS role
    FROM group_members gm
    JOIN users u ON gm.user_id = u.id
    JOIN group_list g ON gm.group_id = g.id
    WHERE gm.group_id = ?
");
$members->execute([$group_id]);
$memberList = $members->fetchAll(PDO::FETCH_ASSOC);

// Lấy nhiệm vụ trong nhóm
$tasks = $conn->prepare("
    SELECT t.*, u.name AS assigned_name
    FROM tasks t
    JOIN users u ON t.assigned_to = u.id
    WHERE t.group_id = ?
");
$tasks->execute([$group_id]);
$taskList = $tasks->fetchAll(PDO::FETCH_ASSOC);

// Lấy chat nhóm
$chat = $conn->prepare("
    SELECT c.*, u.name 
    FROM chat_messages c
    JOIN users u ON c.user_id = u.id
    WHERE c.group_id = ?
    ORDER BY c.created_at ASC
");
$chat->execute([$group_id]);
$chatList = $chat->fetchAll(PDO::FETCH_ASSOC);

// Xử lý gửi chat
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["message"])) {
    $msg = trim($_POST["message"]);
    if ($msg != "") {
        $insert = $conn->prepare("
            INSERT INTO chat_messages (group_id, user_id, message)
            VALUES (?, ?, ?)
        ");
        $insert->execute([$group_id, $user_id, $msg]);
        header("Location: group_detail.php?id=$group_id");
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<title>Chi tiết nhóm</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">

<style>
    body { background: #f0f2f5; }
    .sidebar {
        width: 240px; height: 100vh;
        background: #1f2937;
        position: fixed; top: 0; left: 0;
        padding-top: 20px; color: white;
    }
    .sidebar a {
        color: #e5e7eb; text-decoration: none;
        display: block; padding: 12px 20px;
    }
    .sidebar a:hover { background: #374151; }
    .sidebar .active { background: #111827; }
    .main { margin-left: 240px; padding: 25px; }
</style>
</head>

<body>

<!-- SIDEBAR -->
<div class="sidebar">
    <h3 class="text-center mb-4">⚡ WebNhóm</h3>
    <a href="dashboard.php">Dashboard</a>
    <a href="groups.php" class="active">Nhóm của tôi</a>
    <a href="create_group.php">Tạo nhóm</a>
    <a href="tasks.php">Nhiệm vụ</a>
    <a href="profile.php">Hồ sơ</a>
    <a href="../logout.php" class="text-danger">Đăng xuất</a>
</div>

<!-- MAIN CONTENT -->
<div class="main">
    <h2 class="fw-bold"><?php echo $group["name"]; ?></h2>
    <p><?php echo $group["description"]; ?></p>

    <p><strong>Mã tham gia:</strong> <span class="text-primary"><?php echo $group["join_code"]; ?></span>
    <br>
    <strong>Deadline:</strong> <?php echo $group["deadline"] ?: "Chưa đặt"; ?></p>

    <hr>

    <!-- Thành viên -->
    <h4 class="mt-4">Thành viên</h4>
    <ul class="list-group mb-4">
        <?php foreach ($memberList as $m): ?>
            <li class="list-group-item d-flex justify-content-between">
                <?php echo $m["name"]; ?>
                <span class="badge 
                    <?php echo ($m["role"] == "Leader") ? "bg-danger" : "bg-secondary"; ?>">
                    <?php echo $m["role"]; ?>
                </span>
            </li>
        <?php endforeach; ?>
    </ul>

    <!-- Nhiệm vụ -->
    <h4>Nhiệm vụ</h4>
    <a href="create_task.php?group=<?php echo $group_id; ?>" class="btn btn-primary mb-2">
        <i class="fa fa-plus"></i> Giao nhiệm vụ
    </a>

    <table class="table table-bordered bg-white">
        <tr class="table-light">
            <th>Tên nhiệm vụ</th>
            <th>Giao cho</th>
            <th>Deadline</th>
            <th>Trạng thái</th>
        </tr>

        <?php foreach ($taskList as $t): ?>
            <tr>
                <td><?php echo $t["name"]; ?></td>
                <td><?php echo $t["assigned_name"]; ?></td>
                <td><?php echo $t["deadline"]; ?></td>
                <td>
                    <span class="badge bg-<?php
                        if ($t["status"] == 'done') echo 'success';
                        else if ($t["status"] == 'late') echo 'danger';
                        else echo 'warning';
                    ?>">
                    <?php echo $t["status"]; ?>
                    </span>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>

    <hr>

    <!-- Chat nhóm -->
    <h4>Chat nhóm</h4>

    <div class="border p-3 mb-3 bg-white" style="max-height: 300px; overflow-y: auto;">
        <?php foreach ($chatList as $c): ?>
            <p><strong><?php echo $c["name"]; ?>:</strong> <?php echo $c["message"]; ?>
            <br><small class="text-muted"><?php echo $c["created_at"]; ?></small></p>
            <hr>
        <?php endforeach; ?>
    </div>

    <form method="POST">
        <div class="input-group">
            <input type="text" name="message" class="form-control" placeholder="Nhập tin nhắn..." required>
            <button class="btn btn-primary">
                <i class="fa fa-paper-plane"></i>
            </button>
        </div>
    </form>

</div>
</body>
</html>
