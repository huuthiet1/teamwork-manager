<?php
require_once "../../controllers/check_login.php";
require_once "../../config/Database.php";

$db = new Database();
$conn = $db->connect();

$user_id = $_SESSION["user_id"];
$q = strtolower(trim($_POST["question"]));

/* Hỏi nhiệm vụ hôm nay */
if (str_contains($q,"hôm nay")) {
    $stmt = $conn->prepare("
        SELECT title, deadline 
        FROM tasks 
        WHERE assigned_to = ?
          AND DATE(deadline) = CURDATE()
    ");
    $stmt->execute([$user_id]);
    $tasks = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (!$tasks) {
        echo "Hôm nay bạn không có nhiệm vụ nào 👍";
        exit;
    }

    $res = "📌 Nhiệm vụ hôm nay:\n";
    foreach ($tasks as $t) {
        $res .= "- {$t["title"]} (Hạn: ".date("H:i",strtotime($t["deadline"])).")\n";
    }
    echo $res;
    exit;
}

/* Deadline gần nhất */
if (str_contains($q,"deadline") || str_contains($q,"hạn")) {
    $stmt = $conn->prepare("
        SELECT title, deadline 
        FROM tasks 
        WHERE assigned_to = ?
          AND status='doing'
        ORDER BY deadline ASC LIMIT 1
    ");
    $stmt->execute([$user_id]);
    $t = $stmt->fetch();

    if (!$t) {
        echo "🎉 Bạn không có deadline nào sắp tới!";
        exit;
    }

    echo "⏰ Deadline gần nhất: {$t["title"]} – ".date("d/m/Y H:i",strtotime($t["deadline"]));
    exit;
}

/* Trễ hạn */
if (str_contains($q,"trễ")) {
    $stmt = $conn->prepare("
        SELECT title FROM tasks
        WHERE assigned_to = ?
          AND deadline < NOW()
          AND status!='done'
    ");
    $stmt->execute([$user_id]);
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (!$rows) {
        echo "Không có nhiệm vụ trễ hạn 👍";
        exit;
    }

    echo "⚠️ Nhiệm vụ trễ hạn:\n";
    foreach ($rows as $r) {
        echo "- {$r["title"]}\n";
    }
    exit;
}

echo "🤖 Tôi có thể giúp bạn xem nhiệm vụ hôm nay, deadline, nhiệm vụ trễ hạn.";
