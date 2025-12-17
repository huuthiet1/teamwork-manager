<?php
session_start();
require_once "../config/Database.php";

$user_id = $_SESSION["user_id"];

$group_id    = $_POST["group_id"];
$title       = trim($_POST["title"]);
$description = trim($_POST["description"]);
$assigned_to = $_POST["assigned_to"];
$deadline    = $_POST["deadline"];
$difficulty  = $_POST["difficulty"];
$priority    = $_POST["priority"];

$db = new Database();
$conn = $db->connect();

/* Tạo nhiệm vụ */
$stmt = $conn->prepare("
    INSERT INTO tasks 
    (group_id, assigned_to, created_by, title, description, difficulty, priority, deadline)
    VALUES (?,?,?,?,?,?,?,?)
");
$stmt->execute([
    $group_id,
    $assigned_to,
    $user_id,
    $title,
    $description,
    $difficulty,
    $priority,
    $deadline
]);

/* Ghi log */
$stmt = $conn->prepare("
    INSERT INTO activity_logs (user_id, group_id, action)
    VALUES (?,?,?)
");
$stmt->execute([
    $user_id,
    $group_id,
    "Giao nhiệm vụ: {$title}"
]);

header("Location: ../pages/tasks/index.php");
exit;
