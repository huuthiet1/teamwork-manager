<?php
session_start();
require_once "../config/Database.php";

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: ../pages/groups/create.php");
    exit;
}

$name        = trim($_POST["name"] ?? "");
$description = trim($_POST["description"] ?? "");
$deadline    = $_POST["deadline"] ?: null;
$user_id     = $_SESSION["user_id"];

if ($name === "") {
    $_SESSION["error"] = "Tên nhóm không được để trống";
    header("Location: ../pages/groups/create.php");
    exit;
}

$join_code = random_int(100000, 999999);

try {
    $db   = new Database();
    $conn = $db->connect();

    /* Tạo nhóm */
    $stmt = $conn->prepare("
        INSERT INTO group_list (name, description, join_code, leader_id, deadline)
        VALUES (:name, :des, :code, :leader, :deadline)
    ");
    $stmt->execute([
        "name"     => $name,
        "des"      => $description,
        "code"     => $join_code,
        "leader"   => $user_id,
        "deadline" => $deadline
    ]);

    $group_id = $conn->lastInsertId();

    /* Thêm leader vào group_members */
    $stmt = $conn->prepare("
        INSERT INTO group_members (group_id, user_id, role)
        VALUES (:gid, :uid, 'leader')
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
        "action" => "Tạo nhóm mới: {$name}"
    ]);

    header("Location: ../pages/groups/detail.php?id=" . $group_id);
    exit;

} catch (PDOException $e) {
    $_SESSION["error"] = "Lỗi tạo nhóm, vui lòng thử lại";
    header("Location: ../pages/groups/create.php");
    exit;
}
