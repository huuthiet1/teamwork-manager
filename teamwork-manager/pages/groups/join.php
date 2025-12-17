<?php
require_once "../../controllers/check_login.php";
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Tham gia nhóm</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">

    <style>
        body { background:#f4f6f9; }
        .card { border-radius:16px; }
    </style>
</head>
<body>

<div class="container mt-5" style="max-width:500px">

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="fw-bold">
            <i class="fa fa-key"></i> Tham gia nhóm
        </h4>
        <a href="../dashboard/index.php" class="btn btn-outline-secondary btn-sm">
            ⬅ Quay lại
        </a>
    </div>

    <?php
    if (isset($_SESSION["error"])) {
        echo "<div class='alert alert-danger'>{$_SESSION["error"]}</div>";
        unset($_SESSION["error"]);
    }
    if (isset($_SESSION["success"])) {
        echo "<div class='alert alert-success'>{$_SESSION["success"]}</div>";
        unset($_SESSION["success"]);
    }
    ?>

    <div class="card shadow">
        <div class="card-body p-4">

            <form action="../../process/process_join_group.php" method="POST">

                <div class="mb-3">
                    <label class="form-label fw-bold">Mã OTP nhóm</label>
                    <input type="text"
                           name="join_code"
                           class="form-control text-center fw-bold"
                           maxlength="6"
                           placeholder="VD: 123456"
                           required>
                </div>

                <div class="alert alert-info">
                    <i class="fa fa-info-circle"></i>
                    Nhập <b>mã OTP 6 số</b> do trưởng nhóm cung cấp để tham gia.
                </div>

                <button class="btn btn-success w-100 py-2">
                    <i class="fa fa-user-plus"></i> Tham gia nhóm
                </button>

            </form>

        </div>
    </div>

</div>

</body>
</html>
