<?php
require_once "../../controllers/check_login.php";
require_once "../../config/Database.php";

$db = new Database();
$conn = $db->connect();

$user_id = (int)$_SESSION["user_id"];

/* ================= LẤY NHÓM ================= */
$stmt = $conn->prepare("
    SELECT g.id, g.name
    FROM group_list g
    JOIN group_members gm ON gm.group_id = g.id
    WHERE gm.user_id = ?
    ORDER BY g.created_at DESC
");
$stmt->execute([$user_id]);
$groups = $stmt->fetchAll(PDO::FETCH_ASSOC);

$group_id = (int)($_GET["group_id"] ?? ($groups[0]["id"] ?? 0));
$sent = isset($_GET["sent"]);
?>
<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<title>Báo cáo nhóm</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

<style>
body { background:#f4f6f9; }
.card { border-radius:14px; }
.section-title {
    font-weight:600;
    margin-bottom:12px;
}
</style>

<script>
function confirmSend(){
    return confirm("Bạn chắc chắn muốn gửi báo cáo PDF qua email?");
}
</script>
</head>
<body>

<div class="container mt-4 mb-5">

<!-- TIÊU ĐỀ -->
<h3 class="fw-bold mb-1">📄 Báo cáo tiến độ nhóm</h3>
<p class="text-muted">
    Xuất báo cáo PDF, gửi cho giảng viên và các thành viên
</p>

<?php if($sent): ?>
<div class="alert alert-success">
    ✅ Báo cáo PDF đã được gửi thành công!
</div>
<?php endif; ?>

<!-- CHỌN NHÓM -->
<div class="card shadow-sm mb-4">
<div class="card-body">
<div class="section-title">1️⃣ Chọn nhóm</div>

<form method="GET">
    <select name="group_id" class="form-select" onchange="this.form.submit()">
        <?php foreach($groups as $g): ?>
            <option value="<?= $g["id"] ?>" <?= $g["id"]==$group_id?"selected":"" ?>>
                <?= htmlspecialchars($g["name"]) ?>
            </option>
        <?php endforeach; ?>
    </select>
</form>
</div>
</div>

<?php if($group_id): ?>

<!-- XEM TRƯỚC PDF -->
<div class="card shadow-sm mb-4">
<div class="card-body">
<div class="section-title">2️⃣ Xem trước báo cáo PDF</div>

<form method="GET" action="export_pdf.php" target="_blank">
    <input type="hidden" name="group_id" value="<?= $group_id ?>">
    <button class="btn btn-outline-primary w-100">
        👁️ Xem trước báo cáo PDF
    </button>
</form>
</div>
</div>

<!-- GỬI EMAIL NHÓM -->
<div class="card shadow-sm mb-4">
<div class="card-body">
<div class="section-title">3️⃣ Gửi báo cáo cho giảng viên</div>

<form method="POST" action="send_pdf_mail.php" onsubmit="return confirmSend()">
    <input type="hidden" name="group_id" value="<?= $group_id ?>">

    <label class="form-label fw-semibold">Email giảng viên</label>
    <input type="email"
           name="teacher_email"
           class="form-control mb-3"
           placeholder="giangvien@gmail.com"
           required>

    <button class="btn btn-danger w-100">
        📧 Gửi báo cáo PDF (nhóm)
    </button>
</form>
</div>
</div>

<!-- GỬI THEO TỪNG THÀNH VIÊN -->
<div class="card shadow-sm mb-4">
<div class="card-body">
<div class="section-title">4️⃣ Gửi báo cáo theo từng thành viên</div>

<p class="text-muted mb-2">
    Mỗi thành viên sẽ nhận <strong>PDF cá nhân</strong> riêng
</p>

<form method="POST" action="send_member_reports.php" onsubmit="return confirmSend()">
    <input type="hidden" name="group_id" value="<?= $group_id ?>">
    <button class="btn btn-warning w-100">
        👤 Gửi báo cáo cho từng thành viên
    </button>
</form>
</div>
</div>

<!-- LỊCH SỬ -->
<div class="text-center">
    <a href="history.php" class="btn btn-link">
        📝 Xem lịch sử gửi báo cáo
    </a>
</div>

<?php else: ?>

<div class="alert alert-warning">
    Bạn chưa tham gia nhóm nào.
</div>

<?php endif; ?>

</div>
</body>
</html>
