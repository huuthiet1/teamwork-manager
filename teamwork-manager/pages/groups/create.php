<?php
require_once "../../controllers/check_login.php";
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Tạo nhóm</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">

    <style>
        body { background:#f4f6f9; }
        .card { border-radius:16px; }
    </style>
</head>
<body>

<div class="container mt-5" style="max-width:650px">

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="fw-bold">
            <i class="fa fa-users"></i> Tạo nhóm làm việc
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
    ?>

    <div class="card shadow">
        <div class="card-body p-4">

            <form action="../../process/process_create_group.php" method="POST">

                <div class="mb-3">
                    <label class="form-label fw-bold">Tên nhóm</label>
                    <input type="text" name="name" class="form-control" placeholder="VD: Nhóm đồ án Web" required>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">Mô tả nhóm</label>
                    <textarea name="description" class="form-control" rows="3"
                              placeholder="Mô tả mục tiêu, nội dung làm việc của nhóm"></textarea>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">Deadline nhóm (tuỳ chọn)</label>
                    <input type="datetime-local" name="deadline" class="form-control">
                </div>

                <div class="alert alert-info">
                    <i class="fa fa-info-circle"></i>
                    Sau khi tạo nhóm, hệ thống sẽ tự sinh <b>mã OTP 6 số</b> để mời thành viên tham gia.
                </div>

                <button class="btn btn-primary w-100 py-2">
                    <i class="fa fa-plus"></i> Tạo nhóm
                </button>

            </form>

        </div>
    </div>

</div>

</body>
</html>
