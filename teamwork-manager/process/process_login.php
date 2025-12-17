<?php
session_start();
require_once "../config/Database.php";

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: ../pages/auth/login.php");
    exit;
}

$email    = trim($_POST["email"] ?? "");
$password = trim($_POST["password"] ?? "");

if ($email === "" || $password === "") {
    $_SESSION["error"] = "Vui lòng nhập email và mật khẩu";
    header("Location: ../pages/auth/login.php");
    exit;
}

try {
    $db = new Database();
    $conn = $db->connect();

    $stmt = $conn->prepare("
        SELECT id, name, email, password, role
        FROM users
        WHERE email = :email
        LIMIT 1
    ");
    $stmt->execute(["email" => $email]);

    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user || !password_verify($password, $user["password"])) {
        $_SESSION["error"] = "Email hoặc mật khẩu không đúng";
        header("Location: ../pages/auth/login.php");
        exit;
    }

    // Login OK
    $_SESSION["user_id"] = $user["id"];
    $_SESSION["name"]    = $user["name"];
    $_SESSION["email"]   = $user["email"];
    $_SESSION["role"]    = $user["role"];

    // Update last_seen
    $conn->prepare("UPDATE users SET last_seen = NOW() WHERE id = ?")
         ->execute([$user["id"]]);

    header("Location: ../pages/dashboard/index.php");
    exit;

} catch (PDOException $e) {
    $_SESSION["error"] = "Lỗi hệ thống";
    header("Location: ../pages/auth/login.php");
    exit;
}
