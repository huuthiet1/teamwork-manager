<?php
session_start();
require_once "../config/database.php";

$db = new Database();
$conn = $db->connect();

$group_id = $_GET["group"];
$user_id  = $_SESSION["user_id"];

$stmt = $conn->prepare("
    SELECT c.*, u.name FROM chat_messages c
    JOIN users u ON c.user_id = u.id
    WHERE group_id = ?
    ORDER BY created_at ASC
");
$stmt->execute([$group_id]);
$messages = $stmt->fetchAll(PDO::FETCH_ASSOC);

foreach ($messages as $m) {

    $class = ($m["user_id"] == $user_id) ? "me" : "other";

    echo "<div class='msg $class'>";
    echo "<strong>{$m['name']}</strong><br>";

    // Nội dung text
    if (!empty($m["message"])) {
        echo nl2br($m["message"]) . "<br>";
    }

    // Nếu có file
    if (!empty($m["file_path"])) {

        $ext = strtolower(pathinfo($m["file_path"], PATHINFO_EXTENSION));

        // Ảnh
        if (in_array($ext, ["jpg","jpeg","png","gif"])) {
            echo "<img src='../uploads/chat/{$m['file_path']}' class='preview-img'>";
        }

        // Video
        else if (in_array($ext, ["mp4","mov","avi"])) {
            echo "<video class='preview-video' controls>
                    <source src='../uploads/chat/{$m['file_path']}'>
                  </video>";
        }

        // File
        else {
            echo "<a href='../uploads/chat/{$m['file_path']}' download class='btn btn-sm btn-outline-primary mt-2'>
                    <i class='fa fa-download'></i> Tải file
                  </a>";
        }
    }

    echo "<br><small>{$m['created_at']}</small>";
    echo "</div>";
}
