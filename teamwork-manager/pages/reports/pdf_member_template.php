<?php
if (!isset($group_id,$member_id) || $group_id<=0 || $member_id<=0) die("Missing params");

require_once "../../config/Database.php";
$db = new Database();
$conn = $db->connect();

$stmt = $conn->prepare("SELECT name FROM group_list WHERE id=?");
$stmt->execute([$group_id]);
$groupName = $stmt->fetchColumn();

$stmt = $conn->prepare("SELECT name,email FROM users WHERE id=?");
$stmt->execute([$member_id]);
$member = $stmt->fetch(PDO::FETCH_ASSOC);

$stmt = $conn->prepare("
  SELECT title, deadline, status, difficulty, priority
  FROM tasks
  WHERE group_id=? AND assigned_to=?
  ORDER BY deadline ASC
");
$stmt->execute([$group_id,$member_id]);
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
.small{ text-align:center; font-size:12px; margin-bottom:14px; }
table{ width:100%; border-collapse:collapse; margin:10px 0 18px 0; }
th,td{ border:1px solid #000; padding:6px; }
th{ background:#f0f0f0; text-align:center; }
.center{ text-align:center; }
</style>
</head>
<body>

<h2>BÁO CÁO CÁ NHÂN</h2>
<div class="small">
  Nhóm: <b><?= htmlspecialchars($groupName ?: ("#".$group_id)) ?></b><br>
  Thành viên: <b><?= htmlspecialchars($member["name"] ?? "N/A") ?></b> (<?= htmlspecialchars($member["email"] ?? "") ?>)
</div>

<table>
<tr><th>Nhiệm vụ</th><th>Deadline</th><th>Ưu tiên</th><th>Độ khó</th><th>Trạng thái</th></tr>
<?php if($tasks): foreach($tasks as $t): ?>
<tr>
  <td><?= htmlspecialchars($t["title"]) ?></td>
  <td class="center"><?= date("d/m/Y", strtotime($t["deadline"])) ?></td>
  <td class="center"><?= htmlspecialchars($t["priority"]) ?></td>
  <td class="center"><?= (int)$t["difficulty"] ?></td>
  <td class="center"><?= statusVN($t["status"]) ?></td>
</tr>
<?php endforeach; else: ?>
<tr><td colspan="5" class="center">Chưa có nhiệm vụ được giao</td></tr>
<?php endif; ?>
</table>

</body>
</html>
