<?php
require_once "../../config/Database.php";

function ai_build_group_email(int $group_id): array {
    $db = new Database();
    $conn = $db->connect();

    $stmt = $conn->prepare("SELECT name, deadline FROM group_list WHERE id=?");
    $stmt->execute([$group_id]);
    $group = $stmt->fetch(PDO::FETCH_ASSOC);
    $groupName = $group ? $group["name"] : ("Nhóm #".$group_id);
    $groupDeadline = $group && $group["deadline"] ? date("d/m/Y", strtotime($group["deadline"])) : "Chưa thiết lập";

    $total = (int)$conn->query("SELECT COUNT(*) FROM tasks WHERE group_id=$group_id")->fetchColumn();
    $done  = (int)$conn->query("SELECT COUNT(*) FROM tasks WHERE group_id=$group_id AND status='done'")->fetchColumn();
    $late  = (int)$conn->query("SELECT COUNT(*) FROM tasks WHERE group_id=$group_id AND status='late'")->fetchColumn();
    $doing = (int)$conn->query("SELECT COUNT(*) FROM tasks WHERE group_id=$group_id AND status='doing'")->fetchColumn();

    $stmt = $conn->prepare("
        SELECT u.name, u.email,
               COALESCE(cs.score,0) score,
               COALESCE(cs.percentage,0) percentage
        FROM group_members gm
        JOIN users u ON u.id = gm.user_id
        LEFT JOIN contribution_scores cs ON cs.user_id=u.id AND cs.group_id=gm.group_id
        WHERE gm.group_id = ?
        ORDER BY percentage DESC, score DESC
    ");
    $stmt->execute([$group_id]);
    $members = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $lines = [];
    foreach ($members as $m) {
        $lines[] = "- {$m['name']} ({$m['email']}): {$m['percentage']}% | điểm {$m['score']}";
    }
    $memberBlock = $lines ? implode("\n", $lines) : "- (Chưa có dữ liệu đóng góp)";

    $subject = "Báo cáo tiến độ nhóm: {$groupName}";
    $body =
"Kính gửi Giảng viên,

Nhóm \"{$groupName}\" xin gửi báo cáo tiến độ làm việc.

1) Tổng quan
- Deadline nhóm: {$groupDeadline}
- Tổng nhiệm vụ: {$total}
- Đang làm: {$doing}
- Hoàn thành: {$done}
- Trễ hạn: {$late}

2) Đóng góp thành viên
{$memberBlock}

Nhóm kính mong giảng viên góp ý để nhóm hoàn thiện tốt hơn.

Trân trọng,
Hệ thống WebNhóm
";

    return ["subject" => $subject, "body" => $body];
}

function ai_build_member_email(int $group_id, array $memberRow): array {
    $name = $memberRow["name"] ?? "Thành viên";
    $subject = "Báo cáo cá nhân trong nhóm (PDF đính kèm)";
    $body =
"Kính gửi {$name},

Đây là báo cáo cá nhân của bạn trong nhóm (file PDF đính kèm).
Vui lòng kiểm tra các nhiệm vụ được giao và hoàn thành đúng hạn.

Trân trọng,
Hệ thống WebNhóm
";
    return ["subject" => $subject, "body" => $body];
}
