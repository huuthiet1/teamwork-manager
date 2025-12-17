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
$task_id = $_GET["id"] ?? 0;

// Lấy thông tin nhiệm vụ
$stmt = $conn->prepare("
    SELECT t.*, 
           g.name AS group_name,
           u.name AS assigned_name
    FROM tasks t
    JOIN group_list g ON t.group_id = g.id
    JOIN users u ON t.assigned_to = u.id
    WHERE t.id = ?
");
$stmt->execute([$task_id]);
$task = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$task) {
    die("❌ Nhiệm vụ không tồn tại!");
}

// Lấy lịch sử nộp bài
$sub = $conn->prepare("
    SELECT * FROM task_submissions 
    WHERE task_id = ? 
    ORDER BY submitted_at DESC
");
$sub->execute([$task_id]);
$submissions = $sub->fetchAll(PDO::FETCH_ASSOC);

// Khi user upload minh chứng
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_FILES["file_upload"])) {

    $allowed = ["jpg","png","jpeg","mp3","wav","mp4","pdf","docx","txt","zip"];
    $ext = strtolower(pathinfo($_FILES["file_upload"]["name"], PATHINFO_EXTENSION));

    if (!in_array($ext, $allowed)) {
        die("❌ Định dạng file không được hỗ trợ.");
    }

    $upload_dir = "../uploads/files/";
    $file_name = time() . "_" . $_FILES["file_upload"]["name"];
    $path = $upload_dir . $file_name;

    move_uploaded_file($_FILES["file_upload"]["tmp_name"], $path);

    // Kiểm tra đúng hạn
    $on_time = (date("Y-m-d H:i:s") <= $task["deadline"]) ? 1 : 0;

    // Lưu vào task_submissions
    $insert = $conn->prepare("
        INSERT INTO task_submissions (task_id, user_id, file_path, on_time)
        VALUES (?, ?, ?, ?)
    ");
    $insert->execute([$task_id, $user_id, $file_name, $on_time]);

    header("Location: task_detail.php?id=$task_id");
    exit;
}

// Đánh dấu hoàn thành
if (isset($_POST["complete"])) {
    $status = (date("Y-m-d H:i:s") > $task["deadline"]) ? "late" : "done";

    $update = $conn->prepare("UPDATE tasks SET status = ? WHERE id = ?");
    $update->execute([$status, $task_id]);

    header("Location: task_detail.php?id=$task_id");
    exit;
}

?>

<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<title>Chi tiết nhiệm vụ</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">

<style>
body { background: #f0f2f5; }
.sidebar { width: 240px; height: 100vh; background: #1f2937; position: fixed; left:0; top:0; padding-top:20px; }
.sidebar a { display:block; padding:12px 20px; color:#e5e7eb; text-decoration:none; }
.sidebar .active { background:#111827; }
.main { margin-left:240px; padding:25px; }
.file-box { background:white; padding:12px; border-radius:8px; border:1px solid #ddd; }
</style>
</head>

<body>

<!-- SIDEBAR -->
<div class="sidebar">
    <h3 class="text-center text-white mb-4">⚡ WebNhóm</h3>
    <a href="dashboard.php">Dashboard</a>
    <a href="groups.php">Nhóm của tôi</a>
    <a href="tasks.php" class="active">Nhiệm vụ</a>
    <a href="../logout.php" class="text-danger">Đăng xuất</a>
</div>

<!-- MAIN CONTENT -->
<div class="main">

<h2 class="fw-bold"><?php echo $task["name"]; ?></h2>
<p><?php echo $task["description"]; ?></p>

<p><strong>Nhóm:</strong> <?php echo $task["group_name"]; ?></p>
<p><strong>Giao cho:</strong> <?php echo $task["assigned_name"]; ?></p>
<p><strong>Deadline:</strong> <?php echo $task["deadline"]; ?></p>
<p><strong>Độ khó:</strong> <?php echo $task["difficulty"]; ?>/5</p>

<p>
    <strong>Trạng thái:</strong>
    <span class="badge bg-<?php
        if ($task["status"] == "done") echo "success";
        else if ($task["status"] == "late") echo "danger";
        else echo "warning";
    ?>">
        <?php echo $task["status"]; ?>
    </span>
</p>

<hr>

<!-- Nộp bài -->
<h4>Nộp bài minh chứng</h4>

<form method="POST" enctype="multipart/form-data" class="mb-4">
    <div class="mb-3">
        <input type="file" name="file_upload" class="form-control" required>
    </div>

    <button class="btn btn-primary">
        <i class="fa fa-upload"></i> Upload File
    </button>

    <button name="complete" class="btn btn-success ms-2">
        <i class="fa fa-check"></i> Đánh dấu hoàn thành
    </button>
</form>

<hr>

<!-- Lịch sử nộp -->
<h4>Lịch sử nộp bài</h4>

<?php foreach ($submissions as $s): ?>
    <div class="file-box mb-2">
        <p><strong>File:</strong> <?php echo $s["file_path"]; ?></p>
        <p><strong>Nộp lúc:</strong> <?php echo $s["submitted_at"]; ?></p>
        <p>
            <strong>Tình trạng:</strong>
            <span class="badge bg-<?php echo $s["on_time"] ? "success" : "danger"; ?>">
                <?php echo $s["on_time"] ? "Đúng hạn" : "Trễ hạn"; ?>
            </span>
        </p>
        <a href="../uploads/files/<?php echo $s["file_path"]; ?>" 
           class="btn btn-outline-primary btn-sm" download>
            <i class="fa fa-download"></i> Tải xuống
        </a>
    </div>
<?php endforeach; ?>

</div>

</body>
</html>
