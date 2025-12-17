<?php
session_start();

// Nếu đã đăng nhập thì không cho quay lại login
if (isset($_SESSION["user_id"])) {
    header("Location: ../dashboard/index.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Đăng nhập</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container mt-5" style="max-width: 450px;">
    <div class="card shadow">
        <div class="card-body">
            <h3 class="text-center mb-3">Đăng nhập</h3>

            <!-- HIỂN THỊ LỖI -->
            <?php
            if (isset($_SESSION["error"])) {
                echo "<div class='alert alert-danger'>" . $_SESSION["error"] . "</div>";
                unset($_SESSION["error"]);
            }

            if (isset($_SESSION["success"])) {
                echo "<div class='alert alert-success'>" . $_SESSION["success"] . "</div>";
                unset($_SESSION["success"]);
            }
            ?>

            <form action="../../process/process_login.php" method="POST">

                <div class="mb-3">
                    <label class="form-label">Email đăng nhập</label>
                    <input type="email" class="form-control" name="email" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Mật khẩu</label>
                    <input type="password" class="form-control" name="password" required>
                </div>

                <button type="submit" class="btn btn-primary w-100">
                    Đăng nhập
                </button>

                <p class="text-center mt-3 mb-0">
                    Chưa có tài khoản? <a href="register.php">Đăng ký ngay</a>
                </p>
            </form>

        </div>
    </div>
</div>

</body>
</html>
