<?php
if (isset($_FILES["file"])) {

    $ext = pathinfo($_FILES["file"]["name"], PATHINFO_EXTENSION);
    $allowed = ["jpg","png","mp4","pdf","mp3","zip"];

    if (!in_array($ext, $allowed)) {
        die("❌ File không hỗ trợ");
    }

    $new_name = time() . "_" . $_FILES["file"]["name"];
    move_uploaded_file($_FILES["file"]["tmp_name"], "../uploads/chat/" . $new_name);

    echo "File uploaded: " . $new_name;
}
