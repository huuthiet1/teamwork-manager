<?php
session_start();
if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit;
}

require_once "../config/database.php";
$db = new Database();
$conn = $db->connect();

$user_id = $_SESSION["user_id"];
$group_id = $_GET["group"] ?? 0;

// Lấy danh sách username để gợi ý mention
$users = $conn->prepare("
    SELECT u.id, u.name FROM group_members gm
    JOIN users u ON gm.user_id = u.id
    WHERE gm.group_id = ?
");
$users->execute([$group_id]);
$userList = $users->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<title>Chat nhóm nâng cấp</title>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">

<style>
body { background: #f0f2f5; }

.chat-box {
    height: 70vh;
    background: white;
    border: 1px solid #ddd;
    padding: 15px;
    overflow-y: scroll;
    border-radius: 10px;
}

.msg { padding: 10px; margin-bottom: 10px; border-radius: 10px; max-width: 60%; }
.me { background: #3b82f6; color: white; margin-left:auto; text-align:right; }
.other { background: #e5e7eb; }

.preview-img { max-width:150px; border-radius:10px; margin-top:5px; }
.preview-video { max-width:200px; margin-top:5px; border-radius:10px; }

.mention-list {
    position:absolute; 
    background:white;
    border:1px solid #ccc;
    padding:5px;
    display:none;
    max-height:150px;
    overflow-y:auto;
    z-index:50;
}
.mention-item { padding:5px; cursor:pointer; }
.mention-item:hover { background:#f0f0f0; }
</style>
</head>

<body>

<div class="container mt-4">

    <h2 class="mb-3">💬 Chat nhóm (Nâng cấp)</h2>

    <div class="chat-box" id="chatBox"></div>

    <!-- Gợi ý mention -->
    <div id="mentionBox" class="mention-list"></div>

    <form id="sendForm" class="mt-3" enctype="multipart/form-data">

        <div class="input-group mb-2">
            <input type="text" name="message" id="message" class="form-control" placeholder="Nhập tin nhắn hoặc @tên..." autocomplete="off">
        </div>

        <div class="input-group mb-2">
            <input type="file" name="file_upload" id="file_upload" class="form-control">
        </div>

        <input type="hidden" name="group_id" value="<?php echo $group_id; ?>">

        <button class="btn btn-primary w-100">
            <i class="fa fa-paper-plane"></i> Gửi
        </button>
    </form>

</div>

<script>
let userList = <?php echo json_encode($userList); ?>;

// LOAD tin nhắn realtime
setInterval(loadMessages, 1000);
function loadMessages() {
    fetch("../process/get_chat.php?group=<?php echo $group_id; ?>")
    .then(res => res.text())
    .then(data => {
        document.getElementById("chatBox").innerHTML = data;
        document.getElementById("chatBox").scrollTop = 999999;
    });
}

// AJAX gửi tin + file
document.getElementById("sendForm").onsubmit = function(e) {
    e.preventDefault();

    let formData = new FormData(this);

    fetch("../process/send_chat.php", {
        method: "POST",
        body: formData
    }).then(() => {
        document.getElementById("message").value = "";
        document.getElementById("file_upload").value = "";
        loadMessages();
    });
};

// @ Mention
const msgInput = document.getElementById("message");
const mentionBox = document.getElementById("mentionBox");

msgInput.addEventListener("keyup", function() {
    const text = msgInput.value;
    const cursorPos = msgInput.selectionStart;

    if (text.charAt(cursorPos - 1) === "@") {
        let list = "";
        userList.forEach(u => {
            list += `<div class='mention-item' data-name='${u.name}'>@${u.name}</div>`;
        });
        mentionBox.innerHTML = list;
        mentionBox.style.display = "block";
        mentionBox.style.left = msgInput.offsetLeft + "px";
        mentionBox.style.top = (msgInput.offsetTop + 40) + "px";
    }
});

// Chọn mention
document.addEventListener("click", function(e) {
    if (e.target.classList.contains("mention-item")) {
        msgInput.value += e.target.dataset.name + " ";
    }
    mentionBox.style.display = "none";
});
</script>

</body>
</html>
