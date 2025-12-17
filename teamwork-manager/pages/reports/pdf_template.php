<?php
if (!isset($group_id) || $group_id <= 0) die("Missing group_id");

require_once "../../config/Database.php";
$db = new Database();
$conn = $db->connect();

/* group */
$stmt = $conn->prepare("SELECT name, deadline FROM group_list WHERE id=?");
$stmt->execute([$group_id]);
$group = $stmt->fetch(PDO::FETCH_ASSOC);

/* members */
$stmt = $conn->prepare("
    SELECT u.name, u.email,
           COALESCE(cs.score,0) AS score,
           COALESCE(cs.percentage,0) AS percentage
    FROM group_members gm
    JOIN users u ON u.id = gm.user_id
    LEFT JOIN contribution_scores cs ON cs.user_id=u.id AND cs.group_id=gm.group_id
    WHERE gm.group_id = ?
    ORDER BY percentage DESC, score DESC
");
$stmt->execute([$group_id]);
$members = $stmt->fetchAll(PDO::FETCH_ASSOC);

/* tasks */
$stmt = $conn->prepare("
    SELECT t.title, t.deadline, t.status, u.name AS assignee
    FROM tasks t
    JOIN users u ON u.id = t.assigned_to
    WHERE t.group_id = ?
    ORDER BY t.deadline ASC
");
$stmt->execute([$group_id]);
$tasks = $stmt->fetchAll(PDO::FETCH_ASSOC);

function statusVN($s){
    return $s==="done" ? "Hoàn thành" : ($s==="late" ? "Trễ hạn" : "Đang làm");
}
?>
<!doctype html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<style>
@font-face{
  font-family: DejaVu;
  src: url('../../assets/fonts/DejaVuSans.ttf') format('truetype');
}
body{ font-family: DejaVu; font-size:13px; }
h2{ text-align:center; margin:0 0 10px 0; }
.small{ color:#333; font-size:12px; text-align:center; margin-bottom:14px; }
table{ width:100%; border-collapse:collapse; margin:10px 0 18px 0; }
th,td{ border:1px solid #000; padding:6px; }
th{ background:#f0f0f0; text-align:center; }
.center{ text-align:center; }
</style>
</head>
<body>

<h2>BÁO CÁO NHÓM</h2>
<div class="small">
  Nhóm: <b><?= htmlspecialchars($group["name"] ?? ("#".$group_id)) ?></b>
  | Deadline: <b><?= !empty($group["deadline"]) ? date("d/m/Y", strtotime($group["deadline"])) : "Chưa có" ?></b>
</div>

<h4>Thành viên & mức độ đóng góp</h4>
<table>
<tr><th>Họ tên</th><th>Email</th><th>Điểm</th><th>Đóng góp (%)</th></tr>
<?php if($members): foreach($members as $m): ?>
<tr>
  <td><?= htmlspecialchars($m["name"]) ?></td>
  <td><?= htmlspecialchars($m["email"]) ?></td>
  <td class="center"><?= (int)$m["score"] ?></td>
  <td class="center"><?= (float)$m["percentage"] ?>%</td>
</tr>
<?php endforeach; else: ?>
<tr><td colspan="4" class="center">Chưa có dữ liệu</td></tr>
<?php endif; ?>
</table>

<h4>Danh sách nhiệm vụ</h4>
<table>
<tr><th>Tên nhiệm vụ</th><th>Người làm</th><th>Deadline</th><th>Trạng thái</th></tr>
<?php if($tasks): foreach($tasks as $t): ?>
<tr>
  <td><?= htmlspecialchars($t["title"]) ?></td>
  <td><?= htmlspecialchars($t["assignee"]) ?></td>
  <td class="center"><?= date("d/m/Y", strtotime($t["deadline"])) ?></td>
  <td class="center"><?= statusVN($t["status"]) ?></td>
</tr>
<?php endforeach; else: ?>
<tr><td colspan="4" class="center">Chưa có nhiệm vụ</td></tr>
<?php endif; ?>
</table>

</body>
</html>
