<?php
session_start();
require_once "../config/Database.php";

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: ../pages/groups/join.php");
    exit;
}

$join_code = trim($_POST["join_code"] ?? "");
$user_id   = $_SESSION["user_id"];

/* Kiểm tra mã OTP */
if (!preg_match("/^\d{6}$/", $join_code)) {
    $_SESSION["error"] = "Mã OTP phải gồm 6 chữ số";
    header("Location: ../pages/groups/join.php");
    exit;
}

try {
    $db   = new Database();
    $conn = $db->connect();

    /* Tìm nhóm theo OTP */
    $stmt = $conn->prepare("
        SELECT id, join_code_expires_at
        FROM group_list
        WHERE join_code = :code
        LIMIT 1
    ");
    $stmt->execute(["code" => $join_code]);
    $group = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$group) {
        $_SESSION["error"] = "Mã OTP không đúng";
        header("Location: ../pages/groups/join.php");
        exit;
    }

    /* Kiểm tra OTP hết hạn (nếu có dùng) */
    if ($group["join_code_expires_at"] && strtotime($group["join_code_expires_at"]) < time()) {
        $_SESSION["error"] = "Mã OTP đã hết hạn";
        header("Location: ../pages/groups/join.php");
        exit;
    }

    $group_id = $group["id"];

    /* Kiểm tra đã là thành viên chưa */
    $stmt = $conn->prepare("
        SELECT id 
        FROM group_members 
        WHERE group_id = :gid AND user_id = :uid
    ");
    $stmt->execute([
        "gid" => $group_id,
        "uid" => $user_id
    ]);

    if ($stmt->fetch()) {
        $_SESSION["error"] = "Bạn đã là thành viên của nhóm này";
        header("Location: ../pages/groups/join.php");
        exit;
    }

    /* Thêm thành viên vào nhóm */
    $stmt = $conn->prepare("
        INSERT INTO group_members (group_id, user_id, role)
        VALUES (:gid, :uid, 'member')
    ");
    $stmt->execute([
        "gid" => $group_id,
        "uid" => $user_id
    ]);

    /* Ghi log */
    $stmt = $conn->prepare("
        INSERT INTO activity_logs (user_id, group_id, action)
        VALUES (:uid, :gid, :action)
    ");
    $stmt->execute([
        "uid"    => $user_id,
        "gid"    => $group_id,
        "action" => "Tham gia nhóm bằng mã OTP"
    ]);

    $_SESSION["success"] = "🎉 Tham gia nhóm thành công!";
    header("Location: ../pages/groups/detail.php?id=" . $group_id);
    exit;

} catch (PDOException $e) {
    $_SESSION["error"] = "Lỗi hệ thống, vui lòng thử lại";
    header("Location: ../pages/groups/join.php");
    exit;
}
