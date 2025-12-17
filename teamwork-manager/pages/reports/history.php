<?php
require_once "../../controllers/check_login.php";
require_once "../../config/Database.php";

$db = new Database();
$conn = $db->connect();

$stmt = $conn->query("SELECT * FROM report_logs ORDER BY id DESC LIMIT 200");
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!doctype html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<title>Lịch sử gửi báo cáo</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container mt-4">
<h3 class="fw-bold">📝 Lịch sử gửi báo cáo</h3>

<table class="table table-bordered table-striped align-middle mt-3">
<thead class="table-dark">
<tr>
  <th>#</th><th>Group</th><th>Mode</th><th>To</th><th>Status</th><th>Time</th><th>PDF</th>
</tr>
</thead>
<tbody>
<?php foreach($rows as $r): ?>
<tr>
  <td><?= $r["id"] ?></td>
  <td><?= $r["group_id"] ?></td>
  <td><?= htmlspecialchars($r["mode"]) ?></td>
  <td><?= htmlspecialchars($r["to_email"]) ?></td>
  <td>
    <?php if($r["status"]==="sent"): ?>
      <span class="badge bg-success">sent</span>
    <?php else: ?>
      <span class="badge bg-danger">failed</span>
    <?php endif; ?>
  </td>
  <td><?= $r["created_at"] ?></td>
  <td>
    <?php if(!empty($r["pdf_path"])): ?>
      <a href="../../<?= htmlspecialchars($r["pdf_path"]) ?>" target="_blank">Xem</a>
    <?php else: ?>-<?php endif; ?>
  </td>
</tr>
<?php endforeach; ?>
</tbody>
</table>

</div>
</body>
</html>
