<?php
require_once "../vendor/autoload.php";
require_once "../config/Database.php";
require_once "../config/mail.php";
require_once "../pages/ai/ai_email.php";

use Dompdf\Dompdf;
use PHPMailer\PHPMailer\PHPMailer;

$db = new Database();
$conn = $db->connect();
$mailCfg = require "../config/mail.php";

/* Lấy các nhóm tới hạn hôm nay */
$stmt = $conn->prepare("SELECT id FROM group_list WHERE deadline IS NOT NULL AND DATE(deadline)=CURDATE()");
$stmt->execute();
$groupIds = $stmt->fetchAll(PDO::FETCH_COLUMN);

foreach($groupIds as $group_id){
    $group_id = (int)$group_id;

    /* teacher email: lấy từ leader email (hoặc bạn hardcode) */
    $stmt = $conn->prepare("
      SELECT u.email
      FROM group_list g
      JOIN users u ON u.id=g.leader_id
      WHERE g.id=?
    ");
    $stmt->execute([$group_id]);
    $teacher_email = $stmt->fetchColumn();
    if(!$teacher_email) continue;

    /* tạo pdf */
    $saveDir = __DIR__ . "/../uploads/reports";
    if(!is_dir($saveDir)) mkdir($saveDir,0777,true);
    $pdfPath = $saveDir . "/bao_cao_nhom_{$group_id}.pdf";

    ob_start();
    // truyền $group_id vào template
    include __DIR__ . "/../pages/reports/pdf_template.php";
    $html = ob_get_clean();

    $pdf = new Dompdf(["isHtml5ParserEnabled"=>true,"isRemoteEnabled"=>true]);
    $pdf->loadHtml($html,"UTF-8");
    $pdf->setPaper("A4","portrait");
    $pdf->render();
    file_put_contents($pdfPath, $pdf->output());

    /* AI email */
    $ai = ai_build_group_email($group_id);

    /* send */
    $status="sent"; $err=null;
    try{
        $mail = new PHPMailer(true);
        $mail->isSMTP();
        $mail->Host = $mailCfg["host"];
        $mail->SMTPAuth = true;
        $mail->Username = $mailCfg["username"];
        $mail->Password = $mailCfg["password"];
        $mail->SMTPSecure = $mailCfg["secure"];
        $mail->Port = $mailCfg["port"];

        $mail->setFrom($mailCfg["from_email"], $mailCfg["from_name"]);
        $mail->addAddress($teacher_email);
        $mail->Subject = $ai["subject"]." (Tự động)";
        $mail->Body = $ai["body"];
        $mail->addAttachment($pdfPath);
        $mail->send();
    }catch(Exception $ex){
        $status="failed"; $err=$ex->getMessage();
    }

    $stmt = $conn->prepare("
      INSERT INTO report_logs (group_id, mode, to_email, subject, content, pdf_path, status, error_text)
      VALUES (?,?,?,?,?,?,?,?)
    ");
    $stmt->execute([
      $group_id, "group", $teacher_email, $ai["subject"]." (Tự động)", $ai["body"],
      "uploads/reports/bao_cao_nhom_{$group_id}.pdf",
      $status, $err
    ]);

    echo "Group {$group_id}: {$status}\n";
}
