<?php
require_once "../../controllers/check_login.php";
require_once "../../vendor/autoload.php";
require_once "../../config/Database.php";
require_once "../../config/mail.php";

use Dompdf\Dompdf;

/* ================== KIỂM TRA DỮ LIỆU ================== */
$group_id = (int)($_POST['group_id'] ?? 0);
$teacher_email = trim($_POST['teacher_email'] ?? '');

if ($group_id <= 0 || !filter_var($teacher_email, FILTER_VALIDATE_EMAIL)) {
    die("Dữ liệu không hợp lệ");
}

/* ================== KẾT NỐI DATABASE ================== */
$db = new Database();
$conn = $db->connect();

/* ================== TẠO FILE PDF ================== */
ob_start();
include "pdf_template.php"; // file HTML template
$html = ob_get_clean();

$dompdf = new Dompdf([
    'isHtml5ParserEnabled' => true,
    'isRemoteEnabled' => true
]);

$dompdf->loadHtml($html, 'UTF-8');
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();

/* Lưu PDF tạm */
$pdfPath = sys_get_temp_dir() . "/bao_cao_nhom_$group_id.pdf";
file_put_contents($pdfPath, $dompdf->output());

/* ================== LẤY EMAIL THÀNH VIÊN ================== */
$stmt = $conn->prepare("
    SELECT u.email
    FROM group_members gm
    JOIN users u ON u.id = gm.user_id
    WHERE gm.group_id = ?
");
$stmt->execute([$group_id]);
$memberEmails = $stmt->fetchAll(PDO::FETCH_COLUMN);

/* ================== GỬI EMAIL ================== */
$mail = createMailer();

$mail->addAddress($teacher_email); // người nhận chính

foreach ($memberEmails as $email) {
    if ($email !== $teacher_email) {
        $mail->addCC($email);
    }
}

$subject = "Báo cáo tiến độ nhóm #$group_id";
$content = "
Kính gửi Giảng viên và các thành viên,

Đính kèm là báo cáo tiến độ làm việc của nhóm.
Báo cáo bao gồm:
- Danh sách nhiệm vụ
- Trạng thái hoàn thành
- Mức độ đóng góp của từng thành viên

Trân trọng.
";

$mail->Subject = $subject;
$mail->Body    = $content;
$mail->addAttachment($pdfPath);

/* ================== GỬI MAIL + BẮT LỖI ================== */
$status = 'sent';
$errorText = null;

try {
    $mail->send();
} catch (Exception $e) {
    $status = 'failed';
    $errorText = $mail->ErrorInfo;
}

/* ================== LƯU LỊCH SỬ GỬI BÁO CÁO ================== */
$stmt = $conn->prepare("
    INSERT INTO report_logs
    (group_id, mode, to_email, subject, content, pdf_path, status, error_text)
    VALUES (?, ?, ?, ?, ?, ?, ?, ?)
");

$stmt->execute([
    $group_id,
    'group',
    $teacher_email,
    $subject,
    $content,
    $pdfPath,
    $status,
    $errorText
]);

/* ================== XÓA FILE PDF TẠM ================== */
if (file_exists($pdfPath)) {
    unlink($pdfPath);
}

/* ================== CHUYỂN HƯỚNG ================== */
header("Location: index.php?group_id=$group_id&sent=1");
exit;
