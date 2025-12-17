<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

/* ================= CẤU HÌNH EMAIL ================= */

if (!function_exists('createMailer')) {

    function createMailer(): PHPMailer
    {
        $mail = new PHPMailer(true);

        $mail->isSMTP();
        $mail->Host       = "smtp.gmail.com";
        $mail->SMTPAuth   = true;
        $mail->Username   = "xxxxxxxx@gmail.com";   // email của bạn
        $mail->Password   = "xxxxxxxxx";              // app password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;

        $mail->CharSet = "UTF-8";

        $mail->setFrom(
            "xxxxxxxxxx@gmail.com",
            "Web Quản Lý Công Việc Nhóm"
        );

        $mail->isHTML(true);

        return $mail;
    }
}
