<?php
session_start();

// Xóa toàn bộ session
$_SESSION = [];

// Hủy session
session_destroy();

// Quay về trang login
header("Location: login.php");
exit;
