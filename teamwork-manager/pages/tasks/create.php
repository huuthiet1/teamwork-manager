<?php
require_once "../../controllers/check_login.php";
require_once "../../config/Database.php";

$db = new Database();
$conn = $db->connect();

$user_id = $_SESSION["user_id"];
$group_id = $_GET["group_id"] ?? null;

/* Kiểm tra leader */
$stmt = $conn->prepare("
    SELECT role 
    FROM group_members 
    WHERE group_id = ? AND user_id = ?
");
$stmt->execute([$group_id, $user_id]);
$role = $stmt->fetchColumn();

if ($role !== "leader" && $role !== "co_leader") {
    die("Bạn không có quyền giao nhiệm vụ");
}

/* Lấy thành viên nhóm */
$stmt = $conn->prepare("
    SELECT u.id, u.name
    FROM group_members gm
    JOIN users u ON u.id = gm.user_id
    WHERE gm.group_id = ?
");
$stmt->execute([$group_id]);
$members = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<title>Giao nhiệm vụ</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container mt-5" style="max-width:600px">

<h4 class="fw-bold mb-3">➕ Giao nhiệm vụ</h4>

<form action="../../process/process_create_task.php" method="POST">

<input type="hidden" name="group_id" value="<?= $group_id ?>">

<div class="mb-3">
<label class="form-label">Tên nhiệm vụ</label>
<input type="text" name="title" class="form-control" required>
</div>

<div class="mb-3">
<label class="form-label">Mô tả</label>
<textarea name="description" class="form-control"></textarea>
</div>

<div class="mb-3">
<label class="form-label">Giao cho</label>
<select name="assigned_to" class="form-select" required>
<?php foreach($members as $m): ?>
<option value="<?= $m["id"] ?>"><?= $m["name"] ?></option>
<?php endforeach; ?>
</select>
</div>

<div class="row">
<div class="col-md-6 mb-3">
<label class="form-label">Deadline</label>
<input type="datetime-local" name="deadline" class="form-control" required>
</div>
<div class="col-md-6 mb-3">
<label class="form-label">Độ khó (1-5)</label>
<input type="number" name="difficulty" min="1" max="5" class="form-control" required>
</div>
</div>

<div class="mb-3">
<label class="form-label">Ưu tiên</label>
<select name="priority" class="form-select">
<option value="low">Thấp</option>
<option value="medium">Vừa</option>
<option value="high">Cao</option>
</select>
</div>

<button class="btn btn-primary w-100">
<i class="fa fa-plus"></i> Giao nhiệm vụ
</button>

</form>

</div>
</body>
</html>
