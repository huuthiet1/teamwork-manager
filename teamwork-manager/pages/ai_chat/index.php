<?php
require_once "../../controllers/check_login.php";
?>
<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<title>Trợ lý AI</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
.chat-box {
    height: 420px;
    overflow-y: auto;
    background: #fff;
    border-radius: 12px;
    padding: 15px;
}
.msg-ai { background:#f1f3f5; padding:10px; border-radius:10px; margin-bottom:8px; }
.msg-user { background:#0d6efd; color:#fff; padding:10px; border-radius:10px; text-align:right; margin-bottom:8px; }
</style>
</head>
<body>

<div class="container mt-4" style="max-width:800px">
<h4>🤖 Trợ lý AI công việc</h4>

<div id="chatBox" class="chat-box mb-3"></div>

<div class="input-group">
    <input id="question" class="form-control" placeholder="Hỏi AI về nhiệm vụ, deadline...">
    <button class="btn btn-primary" onclick="askAI()">Gửi</button>
</div>
</div>

<script>
function addMsg(text, type){
    const box = document.getElementById("chatBox");
    const div = document.createElement("div");
    div.className = type;
    div.innerText = text;
    box.appendChild(div);
    box.scrollTop = box.scrollHeight;
}

function askAI(){
    const q = document.getElementById("question").value;
    if(!q) return;
    addMsg(q,"msg-user");
    document.getElementById("question").value = "";

    fetch("ask_ai.php",{
        method:"POST",
        headers:{'Content-Type':'application/x-www-form-urlencoded'},
        body:"question="+encodeURIComponent(q)
    })
    .then(r=>r.text())
    .then(ans=> addMsg(ans,"msg-ai"));
}
</script>

</body>
</html>
