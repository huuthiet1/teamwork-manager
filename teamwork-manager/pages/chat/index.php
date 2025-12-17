<?php
require_once "../../controllers/check_login.php";
require_once "../../config/Database.php";

$db = new Database();
$conn = $db->connect();

$user_id = $_SESSION["user_id"];

$stmt = $conn->prepare("
    SELECT g.id, g.name
    FROM group_members gm
    JOIN group_list g ON g.id = gm.group_id
    WHERE gm.user_id = ?
");
$stmt->execute([$user_id]);
$groups = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<title>Chat nhóm</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container mt-5" style="max-width:600px">
<h4 class="fw-bold mb-3">💬 Chọn nhóm để chat</h4>

<?php if (!$groups): ?>
<div class="alert alert-warning">Bạn chưa tham gia nhóm nào</div>
<?php else: ?>
<div class="list-group shadow">
<?php foreach ($groups as $g): ?>
<a href="room.php?group_id=<?= $g["id"] ?>" class="list-group-item list-group-item-action">
👥 <?= htmlspecialchars($g["name"]) ?>
</a>
<?php endforeach; ?>
</div>
<?php endif; ?>

<a href="../dashboard/index.php" class="btn btn-secondary mt-3">⬅ Dashboard</a>
</div>

</body>
</html>
