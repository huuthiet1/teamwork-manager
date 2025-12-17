<?php
require_once "../../controllers/check_login.php";
require_once "../../config/Database.php";

$db   = new Database();
$conn = $db->connect();

$user_id = $_SESSION["user_id"];

/* ================= USER ================= */
$stmt = $conn->prepare("SELECT name, email FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

/* ================= STATS ================= */
$stmt = $conn->prepare("SELECT COUNT(*) FROM group_members WHERE user_id = ?");
$stmt->execute([$user_id]);
$totalGroups = (int)$stmt->fetchColumn();

$stmt = $conn->prepare("SELECT COUNT(*) FROM tasks WHERE assigned_to = ?");
$stmt->execute([$user_id]);
$totalTasks = (int)$stmt->fetchColumn();

$stmt = $conn->prepare("SELECT COUNT(*) FROM tasks WHERE assigned_to = ? AND status='done'");
$stmt->execute([$user_id]);
$doneTasks = (int)$stmt->fetchColumn();

$stmt = $conn->prepare("SELECT COUNT(*) FROM tasks WHERE assigned_to = ? AND status='late'");
$stmt->execute([$user_id]);
$lateTasks = (int)$stmt->fetchColumn();

$progress = ($totalTasks > 0) ? round(($doneTasks / $totalTasks) * 100) : 0;

/* ================= GROUPS ================= */
$stmt = $conn->prepare("
    SELECT g.id, g.name, g.description, gm.role
    FROM group_members gm
    JOIN group_list g ON g.id = gm.group_id
    WHERE gm.user_id = ?
    ORDER BY gm.joined_at DESC
    LIMIT 5
");
$stmt->execute([$user_id]);
$groups = $stmt->fetchAll(PDO::FETCH_ASSOC);

/* ================= TASKS ================= */
$stmt = $conn->prepare("
    SELECT t.title, t.deadline, t.status, g.name AS group_name
    FROM tasks t
    JOIN group_list g ON g.id = t.group_id
    WHERE t.assigned_to = ?
    ORDER BY t.deadline ASC
    LIMIT 6
");
$stmt->execute([$user_id]);
$tasks = $stmt->fetchAll(PDO::FETCH_ASSOC);

function badgeStatus($s){
    return match($s){
        "done"=>"success",
        "late"=>"danger",
        default=>"warning"
    };
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<title>Bảng điều khiển</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">

<style>
body { background:#f4f6f9; }
.sidebar {
    width:260px; min-height:100vh;
    background:linear-gradient(180deg,#4c6ef5,#15aabf);
    position:fixed; left:0; top:0;
    color:white;
}
.sidebar a {
    display:block; padding:12px 20px;
    color:white; text-decoration:none;
}
.sidebar a:hover { background:rgba(255,255,255,.15); }
.content { margin-left:260px; padding:25px; }
.stat { border-radius:16px; padding:20px; color:white; }
.bg1{background:#4c6ef5;}
.bg2{background:#2f9e44;}
.bg3{background:#f08c00;}
.bg4{background:#fa5252;}
</style>
</head>

<body>

<!-- SIDEBAR -->
<div class="sidebar">
    <h4 class="text-center py-3 fw-bold"><i class="fa fa-bars"></i> WebNhóm</h4>
    <a href="index.php"><i class="fa fa-home me-2"></i> Dashboard</a>
    <a href="../groups/create.php"><i class="fa fa-users me-2"></i> Tạo nhóm</a>
    <a href="../groups/join.php"><i class="fa fa-key me-2"></i> Tham gia nhóm</a>
    <a href="../tasks/index.php"><i class="fa fa-tasks me-2"></i> Nhiệm vụ</a>
    <a href="../chat/index.php"><i class="fa fa-comments me-2"></i> Chat nhóm</a>
    <a href="../reports/index.php"><i class="fa fa-file-pdf me-2"></i> Báo cáo</a>
    <hr>
    <a href="../auth/logout.php"><i class="fa fa-sign-out-alt me-2"></i> Đăng xuất</a>
</div>

<!-- CONTENT -->
<div class="content">

<h3 class="fw-bold">Xin chào, <?= htmlspecialchars($user["name"]) ?> 👋</h3>
<p class="text-muted"><?= htmlspecialchars($user["email"]) ?></p>

<!-- STATS -->
<div class="row g-3 mb-4">
    <div class="col-md-3"><div class="stat bg1"><h3><?= $totalGroups ?></h3><p>Nhóm</p></div></div>
    <div class="col-md-3"><div class="stat bg2"><h3><?= $totalTasks ?></h3><p>Nhiệm vụ</p></div></div>
    <div class="col-md-3"><div class="stat bg3"><h3><?= $doneTasks ?></h3><p>Hoàn thành</p></div></div>
    <div class="col-md-3"><div class="stat bg4"><h3><?= $lateTasks ?></h3><p>Trễ hạn</p></div></div>
</div>

<!-- PROGRESS -->
<div class="card mb-4 shadow">
<div class="card-body">
<h5 class="fw-bold">Tiến độ tổng</h5>
<div class="progress" style="height:22px">
<div class="progress-bar bg-success" style="width:<?= $progress ?>%">
<?= $progress ?>%
</div>
</div>
</div>
</div>

<div class="row g-4">
<div class="col-md-6">
<div class="card shadow">
<div class="card-body">
<h5 class="fw-bold mb-3">Nhóm của bạn</h5>
<?php foreach($groups as $g): ?>
<div class="border rounded p-2 mb-2">
<strong><?= htmlspecialchars($g["name"]) ?></strong>
<div class="small text-muted"><?= htmlspecialchars($g["description"] ?: "Không mô tả") ?></div>
</div>
<?php endforeach; ?>
</div>
</div>
</div>

<div class="col-md-6">
<div class="card shadow">
<div class="card-body">
<h5 class="fw-bold mb-3">Nhiệm vụ sắp tới</h5>
<?php foreach($tasks as $t): ?>
<div class="border rounded p-2 mb-2">
<strong><?= htmlspecialchars($t["title"]) ?></strong>
<div class="small text-muted"><?= htmlspecialchars($t["group_name"]) ?> • <?= $t["deadline"] ?></div>
<span class="badge bg-<?= badgeStatus($t["status"]) ?>"><?= $t["status"] ?></span>
</div>
<?php endforeach; ?>
</div>
</div>
</div>
</div>

<!-- ================= AI AUTO ================= -->
<div class="card shadow mt-4">
<div class="card-header fw-bold">🤖 Trợ lý AI – Gợi ý tự động</div>
<div class="card-body">
<div id="aiBox"
     style="height:260px;
     overflow-y:auto;
     background:#f8f9fa;
     border-radius:10px;
     padding:12px;
     white-space:pre-line">
</div>
<div class="text-muted small mt-2">
AI tự động phân tích nhiệm vụ & deadline của bạn
</div>
</div>
</div>

</div>

<script>
function loadAI(){
    fetch("../ai_chat/auto_ai.php")
        .then(r=>r.text())
        .then(html=>{
            document.getElementById("aiBox").innerHTML = html;
        });
}
loadAI();
setInterval(loadAI,30000); // 30s
</script>

</body>
</html>
