<?php
require_once "../../controllers/check_login.php";
require_once "../../config/Database.php";

$db = new Database();
$conn = $db->connect();

$user_id = $_SESSION["user_id"];

/* ================= AI RESPONSE ================= */
$response = "🤖 **Trợ lý AI Công Việc**\n\n";

/* ========= 1. NHIỆM VỤ HÔM NAY ========= */
$stmt = $conn->prepare("
    SELECT title, deadline
    FROM tasks
    WHERE assigned_to = ?
      AND DATE(deadline) = CURDATE()
      AND status != 'done'
");
$stmt->execute([$user_id]);
$todayTasks = $stmt->fetchAll(PDO::FETCH_ASSOC);

if ($todayTasks) {
    $response .= "📌 **Nhiệm vụ cần làm hôm nay:**\n";
    foreach ($todayTasks as $task) {
        $time = date("H:i", strtotime($task["deadline"]));
        $response .= "• {$task['title']} ⏰ {$time}\n";
    }
} else {
    $response .= "✅ **Hôm nay bạn không có nhiệm vụ nào.**\n";
}

/* ========= 2. DEADLINE GẦN NHẤT ========= */
$stmt = $conn->prepare("
    SELECT title, deadline
    FROM tasks
    WHERE assigned_to = ?
      AND status = 'doing'
    ORDER BY deadline ASC
    LIMIT 1
");
$stmt->execute([$user_id]);
$nextDeadline = $stmt->fetch(PDO::FETCH_ASSOC);

if ($nextDeadline) {
    $date = date("d/m/Y H:i", strtotime($nextDeadline["deadline"]));
    $response .= "\n⏰ **Deadline gần nhất:**\n";
    $response .= "• {$nextDeadline['title']} — {$date}\n";
}

/* ========= 3. NHIỆM VỤ TRỄ HẠN ========= */
$stmt = $conn->prepare("
    SELECT title
    FROM tasks
    WHERE assigned_to = ?
      AND deadline < NOW()
      AND status != 'done'
");
$stmt->execute([$user_id]);
$lateTasks = $stmt->fetchAll(PDO::FETCH_ASSOC);

if ($lateTasks) {
    $response .= "\n⚠️ **Nhiệm vụ đang trễ hạn:**\n";
    foreach ($lateTasks as $task) {
        $response .= "• {$task['title']}\n";
    }
}

/* ========= 4. GỢI Ý THÔNG MINH ========= */
$response .= "\n💡 **Gợi ý từ AI:**\n";
$response .= "• Ưu tiên hoàn thành nhiệm vụ có deadline gần\n";
$response .= "• Xử lý nhiệm vụ trễ hạn càng sớm càng tốt\n";
$response .= "• Chia nhỏ công việc nếu nhiệm vụ quá lớn\n";

$response .= "\n✨ *Chúc bạn một ngày làm việc hiệu quả!*";

echo nl2br(htmlspecialchars($response));
