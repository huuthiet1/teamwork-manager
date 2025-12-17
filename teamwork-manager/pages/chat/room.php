<?php
require_once "../../controllers/check_login.php";
require_once "../../config/Database.php";

$db = new Database();
$conn = $db->connect();

$user_id  = (int)$_SESSION["user_id"];
$group_id = (int)($_GET["group_id"] ?? 0);

/* kiểm tra quyền */
$stmt = $conn->prepare("SELECT 1 FROM group_members WHERE user_id=? AND group_id=?");
$stmt->execute([$user_id, $group_id]);
if (!$stmt->fetch()) {
    die("Không có quyền truy cập nhóm này");
}

/* lấy tin nhắn + media */
$stmt = $conn->prepare("
    SELECT cm.*, u.name,
           m.file_path, m.type
    FROM chat_messages cm
    JOIN users u ON u.id = cm.user_id
    LEFT JOIN chat_media m ON m.chat_id = cm.id
    WHERE cm.group_id = ?
    ORDER BY cm.created_at ASC
");
$stmt->execute([$group_id]);
$messages = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<title>Chat nhóm</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

<style>
body { background:#f4f6f9; }
.chat-box {
    height: 420px;
    overflow-y: auto;
    background: #fff;
    border-radius: 14px;
    padding: 15px;
}
.msg { margin-bottom: 14px; }
.me { text-align: right; }
.bubble {
    display: inline-block;
    padding: 10px 14px;
    border-radius: 14px;
    background: #e9ecef;
    max-width: 70%;
    text-align: left;
}
.me .bubble {
    background: #0d6efd;
    color: #fff;
}
audio { width: 220px; margin-top: 6px; }
video { max-width: 300px; border-radius: 8px; margin-top: 6px; }
img { max-width: 220px; border-radius: 8px; margin-top: 6px; }
</style>
</head>

<body>

<div class="container mt-4" style="max-width:900px">

<h4 class="fw-bold mb-3">💬 Chat nhóm</h4>

<div class="chat-box mb-3">
<?php foreach ($messages as $m): ?>
<div class="msg <?= $m["user_id"] === $user_id ? "me" : "" ?>">
    <div class="small text-muted"><?= htmlspecialchars($m["name"]) ?></div>

    <div class="bubble">
        <?= nl2br(htmlspecialchars($m["message"])) ?>

        <?php if (!empty($m["file_path"])): ?>

            <?php if ($m["type"] === "image"): ?>
                <div>
                    <img src="/<?= htmlspecialchars($m["file_path"]) ?>">
                </div>

            <?php elseif ($m["type"] === "video"): ?>
                <div>
                    <video controls>
                        <source src="/<?= htmlspecialchars($m["file_path"]) ?>" type="video/webm">
                        Trình duyệt không hỗ trợ video
                    </video>
                </div>

            <?php elseif ($m["type"] === "audio"): ?>
                <div>
                    <audio controls>
                        <source src="/<?= htmlspecialchars($m["file_path"]) ?>" type="audio/webm">
                        Trình duyệt không hỗ trợ audio
                    </audio>
                </div>
            <?php endif; ?>

        <?php endif; ?>
    </div>
</div>
<?php endforeach; ?>
</div>

<form id="chatForm" action="../../process/process_send_chat.php" method="POST" enctype="multipart/form-data">
<input type="hidden" name="group_id" value="<?= $group_id ?>">
<input id="mediaInput" type="file" name="media" accept="image/*,video/*,audio/*" hidden>

<div class="input-group mb-2">
    <input type="text" name="message" class="form-control" placeholder="Nhập tin nhắn">
    <button class="btn btn-primary">Gửi</button>
</div>

<div class="d-flex gap-2">
    <button type="button" class="btn btn-outline-secondary" onclick="pickFile()">📎 Ảnh / Video</button>
    <button type="button" class="btn btn-danger" onclick="startAudio()">🎙 Ghi âm</button>
    <button type="button" class="btn btn-secondary" onclick="stopAudio()">⏹ Dừng</button>
</div>
</form>

<a href="index.php" class="btn btn-secondary mt-3">⬅ Nhóm khác</a>

</div>

<script>
let recorder, chunks = [], stream;

function pickFile() {
    document.getElementById("mediaInput").click();
}

document.getElementById("mediaInput").addEventListener("change", () => {
    document.getElementById("chatForm").requestSubmit();
});

async function startAudio() {
    try {
        stream = await navigator.mediaDevices.getUserMedia({ audio: true });
        recorder = new MediaRecorder(stream); // KHÔNG set mimeType
        chunks = [];

        recorder.ondataavailable = e => {
            if (e.data.size > 0) chunks.push(e.data);
        };

        recorder.onstop = () => {
            stream.getTracks().forEach(t => t.stop());
            const blob = new Blob(chunks);
            const file = new File([blob], "audio_" + Date.now() + ".webm");

            const dt = new DataTransfer();
            dt.items.add(file);
            document.getElementById("mediaInput").files = dt.files;

            document.getElementById("chatForm").requestSubmit();
        };

        recorder.start();
        alert("🎙 Đang ghi âm… bấm DỪNG để gửi");

    } catch (err) {
        alert("Không truy cập được micro: " + err.message);
    }
}

function stopAudio() {
    if (recorder && recorder.state !== "inactive") {
        recorder.stop();
    }
}
</script>

<style>
audio {
    display: block !important;
    visibility: visible !important;
    opacity: 1 !important;
    width: 240px !important;
    height: 32px !important;
}

audio::-webkit-media-controls {
    display: flex !important;
}

.bubble {
    overflow: visible !important;
}

</body>
</html>
