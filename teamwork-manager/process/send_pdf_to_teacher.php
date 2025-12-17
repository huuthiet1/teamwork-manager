<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);


require_once "../../controllers/check_login.php";
require_once "../../config/Database.php";
require_once "../../vendor/autoload.php";

use Dompdf\Dompdf;
use Dompdf\Options;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$db = new Database();
$conn = $db->connect();

$group_id = (int)($_POST["group_id"] ?? 0);
$teacherEmail = trim($_POST["teacher_email"] ?? "");

if (!$group_id || !$teacherEmail) {
    die("Thiếu dữ liệu");
}

/* ================= THÔNG TIN NHÓM ================= */
$stmt = $conn->prepare("SELECT name, description FROM group_list WHERE id = ?");
$stmt->execute([$group_id]);
$group = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$group) die("Nhóm không tồn tại");

/* ================= EMAIL THÀNH VIÊN ================= */
$stmt = $conn->prepare("
    SELECT DISTINCT u.email
    FROM group_members gm
    JOIN users u ON u.id = gm.user_id
    WHERE gm.group_id = ?
");
$stmt->execute([$group_id]);
$memberEmails = $stmt->fetchAll(PDO::FETCH_COLUMN);

/* ================= NHIỆM VỤ ================= */
$stmt = $conn->prepare("
    SELECT t.title, t.deadline, t.status, u.name assignee
    FROM tasks t
    JOIN users u ON u.id = t.assigned_to
    WHERE t.group_id = ?
");
$stmt->execute([$group_id]);
$tasks = $stmt->fetchAll(PDO::FETCH_ASSOC);

/* ================= HTML PDF ================= */
$html = '
<style>
body{font-family:DejaVu Sans;font-size:12px}
h1{text-align:center}
table{width:100%;border-collapse:collapse}
th,td{border:1px solid #333;padding:6px}
th{background:#eee}
</style>

<h1>BÁO CÁO TIẾN ĐỘ NHÓM</h1>
<h3>'.htmlspecialchars($group["name"]).'</h3>
<p>'.htmlspecialchars($group["description"] ?: "Không mô tả").'</p>

<h4>Danh sách nhiệm vụ</h4>
<table>
<tr>
<th>Nhiệm vụ</th>
<th>Người làm</th>
<th>Deadline</th>
<th>Trạng thái</th>
</tr>';

foreach ($tasks as $t) {
    $html .= "
    <tr>
        <td>{$t['title']}</td>
        <td>{$t['assignee']}</td>
        <td>{$t['deadline']}</td>
        <td>{$t['status']}</td>
    </tr>";
}

$html .= "</table>";

/* ================= TẠO PDF ================= */
$options = new Options();
$options->set('defaultFont','DejaVu Sans');

$dompdf = new Dompdf($options);
$dompdf->loadHtml($html,'UTF-8');
$dompdf->setPaper('A4','portrait');
$dompdf->render();

$pdfPath = "../../uploads/files/bao_cao_nhom_$group_id.pdf";
file_put_contents($pdfPath, $dompdf->output());

/* ================= GỬI EMAIL ================= */
$mail = new PHPMailer(true);

try {
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->Username = 'huuthiettruong35@gmail.com';   // email gửi
    $mail->Password = 'vjcq wutx qran ukgh';           // app password
    $mail->SMTPSecure = 'tls';
    $mail->Port = 587;

    $mail->setFrom('huuthiettruong35@gmail.com', 'WebNhóm');

    // Gửi cho giảng viên (TO)
    $mail->addAddress($teacherEmail);

    // Gửi cho thành viên (BCC)
    foreach ($memberEmails as $email) {
        if ($email !== $teacherEmail) {
            $mail->addBCC($email);
        }
    }

    $mail->Subject = "Báo cáo tiến độ nhóm: ".$group["name"];
    $mail->Body =
        "Kính gửi giảng viên và các thành viên,\n\n".
        "Đính kèm là báo cáo PDF tiến độ nhóm.\n\n".
        "Trân trọng,\nWebNhóm";

    $mail->addAttachment($pdfPath);

    $mail->send();

    echo "✅ Đã gửi PDF cho giảng viên và toàn bộ thành viên trong nhóm.";
} catch (Exception $e) {
    echo "❌ Lỗi gửi mail: ".$mail->ErrorInfo;
}
