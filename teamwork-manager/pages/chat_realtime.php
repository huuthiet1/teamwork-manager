<?php
session_start();
$user = $_SESSION["user_name"];
?>

<!DOCTYPE html>
<html>
<head>
<title>Chat Realtime WebSocket</title>

<style>
#chat {
    width: 100%;
    height: 400px;
    border: 1px solid #aaa;
    padding: 10px;
    overflow-y: scroll;
    background: #f7f7f7;
}
.me { color: blue; }
.other { color: green; }
.mention { color: red; font-weight: bold; }
</style>

</head>
<body>

<h2>💬 Chat Realtime (WebSocket)</h2>

<div id="chat"></div>

<input type="text" id="msg" placeholder="Nhập tin nhắn..." style="width:80%">
<button onclick="sendMsg()">Gửi</button>

<br><br>

<!-- Upload file -->
<form id="uploadForm" method="POST" enctype="multipart/form-data" action="../process/chat_file_upload.php">
    <input type="file" name="file">
    <button>Gửi file</button>
</form>

<script>
let user = "<?php echo $user; ?>";
let ws = new WebSocket("ws://localhost:8080");

ws.onopen = () => console.log("Connected to WebSocket!");

ws.onmessage = (msg) => {
    let data = JSON.parse(msg.data);
    displayMessage(data.user, data.message);
};

function sendMsg() {
    let message = document.getElementById("msg").value;
    if (!message) return;

    // replace @username highlight
    message = message.replace(/@(\w+)/g, "<span class='mention'>@$1</span>");

    ws.send(JSON.stringify({
        user: user,
        message: message
    }));

    displayMessage(user, message);
    document.getElementById("msg").value = "";
}

function displayMessage(sender, message) {
    let chat = document.getElementById("chat");
    let type = (sender == user) ? "me" : "other";
    chat.innerHTML += `
        <p class="${type}">
            <strong>${sender}:</strong> ${message}
        </p>`;
    chat.scrollTop = chat.scrollHeight;
}
</script>

</body>
</html>
