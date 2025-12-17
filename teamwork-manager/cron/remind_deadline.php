<?php
require_once __DIR__ . "/../config/Database.php";
require_once __DIR__ . "/../vendor/autoload.php";

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$db = new Database();
$conn = $db->connect();

/*
 Lấy các nhiệm vụ:
 - chưa hoàn thành
 - deadline trong 24h tới
*/
$stmt = $conn->query("
    SELECT 
        t.title,
        t.deadline,
        u.email,
        u.name
    FROM tasks t
    JOIN users u ON u.id = t.assigned_to
    WHERE t.status = 'doing'
      AND t.deadline BETWEEN NOW() AND DATE_ADD(NOW(), INTERVAL 24 HOUR)
");

while ($task = $stmt->fetch(PDO::FETCH_ASSOC)) {

    $mail = new PHPMailer(true);

    try {
        /* ===== SMTP GMAIL (THEO CẤU HÌNH BẠN ĐƯA) ===== */
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'huuthiettruong35@gmail.com';   // EMAIL_HOST_USER
        $mail->Password   = 'vjcq wutx qran ukgh';           // EMAIL_HOST_PASSWORD (App Password)
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;
        $mail->CharSet    = 'UTF-8';

        /* ===== EMAIL ===== */
        $mail->setFrom(
            'huuthiettruong35@gmail.com',
            'Web-NhomS 🌱'
        );

        $mail->addAddress($task["email"], $task["name"]);

        $mail->isHTML(true);
        $mail->Subject = "⏰ Nhắc deadline nhiệm vụ sắp đến hạn";

        $mail->Body = "
            <h3>Xin chào {$task["name"]},</h3>
            <p>Bạn có nhiệm vụ sắp đến hạn:</p>

            <ul>
                <li><strong>Nhiệm vụ:</strong> {$task["title"]}</li>
                <li><strong>Deadline:</strong> {$task["deadline"]}</li>
            </ul>

            <p>👉 Vui lòng hoàn thành đúng hạn để tránh bị trễ.</p>

            <hr>
            <small>Hệ thống Web Quản Lý Công Việc Nhóm</small>
        ";

        $mail->send();

        echo "✔ Đã gửi mail cho {$task["email"]}\n";

    } catch (Exception $e) {
        echo "❌ Gửi mail thất bại ({$task["email"]}): {$mail->ErrorInfo}\n";
    }
}
