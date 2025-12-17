<?php
session_start();
require_once "../config/Database.php";

if (!isset($_SESSION["user_id"])) {
    header("Location: ../pages/login.php");
    exit;
}

$user_id  = (int)$_SESSION["user_id"];
$group_id = (int)($_POST["group_id"] ?? 0);
$message  = trim($_POST["message"] ?? "");

$db = new Database();
$conn = $db->connect();

/* Lưu tin nhắn (cho phép message rỗng nếu có media) */
$stmt = $conn->prepare("
    INSERT INTO chat_messages (group_id, user_id, message)
    VALUES (:gid, :uid, :msg)
");
$stmt->execute([
    "gid" => $group_id,
    "uid" => $user_id,
    "msg" => $message
]);
$chat_id = $conn->lastInsertId();

/* Upload media */
if (!empty($_FILES["media"]["name"])) {

    $mime = $_FILES["media"]["type"] ?? "";
    $ext  = strtolower(pathinfo($_FILES["media"]["name"], PATHINFO_EXTENSION));
    $mainType = explode("/", $mime)[0];

    /* ÉP KIỂU THEO ĐUÔI FILE (QUAN TRỌNG) */
    if (in_array($ext, ["webm","wav","mp3","ogg"])) {
        $mainType = "audio";
    } elseif (in_array($ext, ["mp4","webm"])) {
        $mainType = "video";
    } elseif (in_array($ext, ["jpg","jpeg","png","gif","webp"])) {
        $mainType = "image";
    }

    if (!in_array($mainType, ["image","video","audio"])) {
        header("Location: ../pages/chat/room.php?group_id=".$group_id);
        exit;
    }

    $uploadDir = __DIR__ . "/../uploads/chat/";
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    $safeName = preg_replace("/[^a-zA-Z0-9._-]/", "_", $_FILES["media"]["name"]);
    $fileName = time() . "_" . $safeName;

    $absolutePath = $uploadDir . $fileName;
    $relativePath = "uploads/chat/" . $fileName;

    if (move_uploaded_file($_FILES["media"]["tmp_name"], $absolutePath)) {
        $stmt = $conn->prepare("
            INSERT INTO chat_media (chat_id, file_path, type)
            VALUES (:cid, :path, :type)
        ");
        $stmt->execute([
            "cid"  => $chat_id,
            "path" => $relativePath,
            "type" => $mainType
        ]);
    }
}

header("Location: ../pages/chat/room.php?group_id=".$group_id);
exit;
