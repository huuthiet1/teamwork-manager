<?php
require_once "../../controllers/check_login.php";
require_once "../../config/Database.php";

$db = new Database();
$conn = $db->connect();

$user_id = $_SESSION["user_id"];

/* Lấy nhiệm vụ liên quan đến user */
$stmt = $conn->prepare("
    SELECT 
        t.id,
        t.title,
        t.deadline,
        t.status,
        t.priority,
        t.difficulty,
        g.name AS group_name,
        g.id AS group_id,
        u.name AS assigned_name
    FROM tasks t
    JOIN group_members gm ON gm.group_id = t.group_id
    JOIN group_list g ON g.id = t.group_id
    JOIN users u ON u.id = t.assigned_to
    WHERE gm.user_id = ?
    ORDER BY 
        (t.status = 'doing') DESC,
        t.deadline ASC
");
$stmt->execute([$user_id]);
$tasks = $stmt->fetchAll(PDO::FETCH_ASSOC);

function statusVN($s) {
    return match($s) {
        "done" => ["Hoàn thành","bg-success"],
        "late" => ["Trễ hạn","bg-danger"],
        default => ["Đang làm","bg-primary"]
    };
}
function priorityVN($p) {
    return match($p) {
        "high" => ["Cao","bg-danger"],
        "low" => ["Thấp","bg-secondary"],
        default => ["Vừa","bg-warning text-dark"]
    };
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<title>Nhiệm vụ</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">

<style>
body { background:#f4f6f9; }
.card { border-radius:16px; }
</style>
</head>

<body>

<div class="container mt-4">

<div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="fw-bold">
        <i class="fa fa-tasks"></i> Danh sách nhiệm vụ
    </h4>
    <a href="../dashboard/index.php" class="btn btn-outline-secondary btn-sm">⬅ Dashboard</a>
</div>

<div class="card shadow">
<div class="card-body">

<?php if(!$tasks): ?>
<div class="alert alert-info">Chưa có nhiệm vụ nào.</div>
<?php else: ?>

<table class="table table-hover align-middle">
<thead class="table-light">
<tr>
    <th>Nhiệm vụ</th>
    <th>Nhóm</th>
    <th>Người làm</th>
    <th>Deadline</th>
    <th>Ưu tiên</th>
    <th>Trạng thái</th>
</tr>
</thead>
<tbody>
<?php foreach($tasks as $t): 
    [$st,$sc] = statusVN($t["status"]);
    [$pt,$pc] = priorityVN($t["priority"]);
?>
<tr>
    <td>
        <strong><?= htmlspecialchars($t["title"]) ?></strong>
        <div class="text-muted small">Độ khó: <?= $t["difficulty"] ?>/5</div>
    </td>
    <td><?= htmlspecialchars($t["group_name"]) ?></td>
    <td><?= htmlspecialchars($t["assigned_name"]) ?></td>
    <td><?= $t["deadline"] ?></td>
    <td><span class="badge <?= $pc ?>"><?= $pt ?></span></td>
    <td><span class="badge <?= $sc ?>"><?= $st ?></span></td>
</tr>
<?php endforeach; ?>
</tbody>
</table>

<?php endif; ?>

</div>
</div>

</div>

</body>
</html>
