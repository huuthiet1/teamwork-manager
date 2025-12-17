<?php
session_start();
require_once "../config/Database.php";

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: ../pages/auth/register.php");
    exit;
}

$name     = trim($_POST["name"] ?? "");
$email    = trim($_POST["email"] ?? "");
$password = trim($_POST["password"] ?? "");

if ($name === "" || $email === "" || $password === "") {
    $_SESSION["error"] = "Vui lòng nhập đầy đủ thông tin";
    header("Location: ../pages/auth/register.php");
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $_SESSION["error"] = "Email không hợp lệ";
    header("Location: ../pages/auth/register.php");
    exit;
}

if (strlen($password) < 6) {
    $_SESSION["error"] = "Mật khẩu phải từ 6 ký tự";
    header("Location: ../pages/auth/register.php");
    exit;
}

try {
    $db = new Database();
    $conn = $db->connect();

    // Kiểm tra email tồn tại
    $check = $conn->prepare("SELECT id FROM users WHERE email = :email");
    $check->execute(["email" => $email]);

    if ($check->fetch()) {
        $_SESSION["error"] = "Email đã tồn tại";
        header("Location: ../pages/auth/register.php");
        exit;
    }

    $passwordHash = password_hash($password, PASSWORD_DEFAULT);

    $insert = $conn->prepare("
        INSERT INTO users (name, email, password, role)
        VALUES (:name, :email, :password, 'member')
    ");

    $insert->execute([
        "name"     => $name,
        "email"    => $email,
        "password" => $passwordHash
    ]);

    $_SESSION["success"] = "Đăng ký thành công! Vui lòng đăng nhập.";
    header("Location: ../pages/auth/login.php");
    exit;

} catch (PDOException $e) {
    $_SESSION["error"] = "Lỗi hệ thống";
    header("Location: ../pages/auth/register.php");
    exit;
}
