<?php

namespace App\Http\Controllers;

use App\Models\TaskAssignment;
use App\Models\MessageRead;
use App\Models\Message;
use App\Models\GroupMember;
use Illuminate\Support\Facades\Auth;

class AiAssistantController extends Controller
{
    /**
     * Dashboard AI – gợi ý hôm nay
     */
    public function dashboard()
    {
        $userId = Auth::id();
        $now = now();

        $suggestions = [];

        /* ===============================
         * 1️⃣ Task được giao – sắp deadline (<24h)
         * =============================== */
        $nearDeadlineTasks = TaskAssignment::with('task.group')
            ->where('user_id', $userId)
            ->where('status', 'doing')
            ->whereHas('task', function ($q) use ($now) {
                $q->whereBetween('deadline', [$now, $now->copy()->addDay()]);
            })
            ->get();

        foreach ($nearDeadlineTasks as $a) {
            $hours = $now->diffInHours($a->task->deadline);

            $suggestions[] = [
                'type' => 'task_deadline',
                'priority' => 'high',
                'message' => "Nhiệm vụ \"{$a->task->title}\" (Nhóm {$a->task->group->name}) còn {$hours} giờ nữa đến hạn.",
                'group_id' => $a->task->group_id,
                'task_id' => $a->task->id,
            ];
        }

        /* ===============================
         * 2️⃣ Task trễ hạn
         * =============================== */
        $lateTasks = TaskAssignment::with('task.group')
            ->where('user_id', $userId)
            ->where('status', 'doing')
            ->whereHas('task', function ($q) use ($now) {
                $q->where('deadline', '<', $now);
            })
            ->get();

        foreach ($lateTasks as $a) {
            $lateHours = $a->task->deadline->diffInHours($now);

            $suggestions[] = [
                'type' => 'task_late',
                'priority' => 'high',
                'message' => "Nhiệm vụ \"{$a->task->title}\" đã trễ {$lateHours} giờ.",
                'group_id' => $a->task->group_id,
                'task_id' => $a->task->id,
            ];
        }

        /* ===============================
         * 3️⃣ Gợi ý chia nhỏ task khó
         * =============================== */
        $hardTasks = TaskAssignment::with('task.group')
            ->where('user_id', $userId)
            ->where('status', 'doing')
            ->whereHas('task', function ($q) {
                $q->where('difficulty', '>=', 4);
            })
            ->get();

        foreach ($hardTasks as $a) {
            $suggestions[] = [
                'type' => 'task_split',
                'priority' => 'normal',
                'message' => "Nhiệm vụ \"{$a->task->title}\" có độ khó cao, nên chia nhỏ thành subtasks.",
                'group_id' => $a->task->group_id,
                'task_id' => $a->task->id,
            ];
        }

        /* ===============================
         * 4️⃣ Tin nhắn chưa đọc (ĐÃ FIX)
         * =============================== */
        $groupIds = GroupMember::where('user_id', $userId)
            ->where('is_active', 1)
            ->pluck('group_id');

        foreach ($groupIds as $gid) {

            $lastReadId = MessageRead::where([
                'group_id' => $gid,
                'user_id'  => $userId,
            ])->value('last_read_message_id') ?? 0;

            $hasUnread = Message::where('group_id', $gid)
                ->where('is_deleted', 0)
                ->where('id', '>', $lastReadId)
                ->exists();

            if ($hasUnread) {
                $suggestions[] = [
                    'type' => 'chat_unread',
                    'priority' => 'normal',
                    'message' => 'Bạn có tin nhắn chưa đọc trong nhóm.',
                    'group_id' => $gid,
                ];
            }
        }

        return response()->json([
            'suggestions' => $suggestions
        ]);
    }
}
