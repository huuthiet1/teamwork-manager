<?php
session_start();
require_once "../config/database.php";

$db = new Database();
$conn = $db->connect();

$user_id  = $_SESSION["user_id"];
$group_id = $_POST["group_id"];
$message  = trim($_POST["message"]);
$file_url = null;

// Xử lý file (nếu có)
if (!empty($_FILES["file_upload"]["name"])) {

    $ext = strtolower(pathinfo($_FILES["file_upload"]["name"], PATHINFO_EXTENSION));
    $allowed = ["jpg","png","jpeg","gif","mp4","mov","avi","mp3","wav","pdf","docx"];

    if (!in_array($ext, $allowed)) {
        die("❌ File không hỗ trợ!");
    }

    $save_name = time() . "_" . $_FILES["file_upload"]["name"];
    $upload_path = "../uploads/chat/" . $save_name;

    if (!is_dir("../uploads/chat")) {
        mkdir("../uploads/chat", 0777, true);
    }

    move_uploaded_file($_FILES["file_upload"]["tmp_name"], $upload_path);
    $file_url = $save_name;
}

// Lưu vào database
$stmt = $conn->prepare("
    INSERT INTO chat_messages (group_id, user_id, message, file_path)
    VALUES (?, ?, ?, ?)
");

$stmt->execute([$group_id, $user_id, $message, $file_url]);
