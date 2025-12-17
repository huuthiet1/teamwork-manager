<?php
require_once "../../controllers/check_login.php";
require_once "../../vendor/autoload.php";
require_once "../../config/Database.php";
require_once "../../config/mail.php";
require_once "../ai/ai_email.php";

use Dompdf\Dompdf;
use PHPMailer\PHPMailer\PHPMailer;

$db = new Database();
$conn = $db->connect();
$mailCfg = require "../../config/mail.php";

$group_id = (int)($_POST["group_id"] ?? 0);
if($group_id<=0) die("Thiếu group_id");

/* danh sách member */
$stmt = $conn->prepare("
  SELECT u.id, u.name, u.email
  FROM group_members gm
  JOIN users u ON u.id=gm.user_id
  WHERE gm.group_id=?
");
$stmt->execute([$group_id]);
$members = $stmt->fetchAll(PDO::FETCH_ASSOC);

$saveDir = __DIR__ . "/../../uploads/reports/members";
if(!is_dir($saveDir)) mkdir($saveDir,0777,true);

foreach($members as $m){
    $member_id = (int)$m["id"];
    $to = trim($m["email"]);
    if(!$to) continue;

    /* tạo pdf cá nhân */
    $pdfPath = $saveDir . "/member_{$member_id}_group_{$group_id}.pdf";
    ob_start();
    include "pdf_member_template.php"; // dùng $group_id, $member_id
    $html = ob_get_clean();

    $pdf = new Dompdf(["isHtml5ParserEnabled"=>true,"isRemoteEnabled"=>true]);
    $pdf->loadHtml($html,"UTF-8");
    $pdf->setPaper("A4","portrait");
    $pdf->render();
    file_put_contents($pdfPath, $pdf->output());

    /* AI email cá nhân */
    $ai = ai_build_member_email($group_id, $m);
    $subject = $ai["subject"];
    $body = $ai["body"];

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
        $mail->addAddress($to);
        $mail->Subject = $subject;
        $mail->Body = $body;
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
      $group_id, "member", $to, $subject, $body,
      "uploads/reports/members/member_{$member_id}_group_{$group_id}.pdf",
      $status, $err
    ]);
}

header("Location: index.php?group_id=".$group_id."&sent=1");
exit;
