<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Models\MessageRead;
use App\Models\WorkGroup;
use App\Models\GroupMember;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class MessageController extends Controller
{
    /**
     * Danh sách tin nhắn theo group (polling)
     */
    public function index(Request $request)
    {
        $request->validate([
            'group_id' => 'required|integer',
            'after_id' => 'nullable|integer'
        ]);

        $group = WorkGroup::find($request->group_id);

        // 1️⃣ Check group
        if (!$group || $group->is_deleted) {
            return response()->json(['message' => 'Nhóm không tồn tại hoặc đã bị đóng'], 403);
        }

        // 2️⃣ Check member active
        $member = GroupMember::where([
            'group_id' => $group->id,
            'user_id'  => Auth::id(),
            'is_active'=> 1
        ])->first();

        if (!$member) {
            return response()->json(['message' => 'Không có quyền truy cập chat'], 403);
        }

        // 3️⃣ Query message
        $query = Message::where('group_id', $group->id)
            ->where('is_deleted', 0)
            ->orderBy('id');

        // polling: chỉ lấy tin mới
        if ($request->filled('after_id')) {
            $query->where('id', '>', $request->after_id);
        }

        $messages = $query->limit(50)->get();

        // 4️⃣ Update read receipt
        if ($messages->isNotEmpty()) {
            $lastMessageId = $messages->last()->id;

            MessageRead::updateOrCreate(
                [
                    'group_id' => $group->id,
                    'user_id'  => Auth::id(),
                ],
                [
                    'last_read_message_id' => $lastMessageId,
                    'last_read_at' => now(),
                ]
            );
        }

        return response()->json([
            'messages' => $messages
        ]);
    }

    /**
     * Gửi tin nhắn
     */
    public function store(Request $request)
    {
        $request->validate([
            'group_id' => 'required|integer',
            'content'  => 'required|string|max:2000',
            'type'     => 'nullable|in:text,image,audio'
        ]);

        $group = WorkGroup::find($request->group_id);

        // 1️⃣ Check group
        if (!$group || $group->is_deleted) {
            return response()->json(['message' => 'Nhóm không tồn tại hoặc đã bị đóng'], 403);
        }

        // 2️⃣ Check member active
        $member = GroupMember::where([
            'group_id' => $group->id,
            'user_id'  => Auth::id(),
            'is_active'=> 1
        ])->first();

        if (!$member) {
            return response()->json(['message' => 'Không có quyền gửi tin'], 403);
        }

        $message = Message::create([
            'group_id' => $group->id,
            'user_id'  => Auth::id(),
            'type'     => $request->type ?? 'text',
            'content'  => $request->content,
        ]);

        // 3️⃣ Update sender read receipt
        MessageRead::updateOrCreate(
            [
                'group_id' => $group->id,
                'user_id'  => Auth::id(),
            ],
            [
                'last_read_message_id' => $message->id,
                'last_read_at' => now(),
            ]
        );

        return response()->json([
            'message' => 'Đã gửi tin nhắn',
            'data' => $message
        ], 201);
    }

    /**
     * Xoá mềm tin nhắn (chỉ chủ tin hoặc leader)
     */
    public function destroy(Message $message)
    {
        $group = $message->group;

        if (!$group || $group->is_deleted) {
            return response()->json(['message' => 'Nhóm đã bị đóng'], 403);
        }

        if (
            $message->user_id !== Auth::id() &&
            $group->leader_id !== Auth::id()
        ) {
            return response()->json(['message' => 'Không có quyền xoá'], 403);
        }

        $message->update(['is_deleted' => 1]);

        return response()->json(['message' => 'Đã xoá tin nhắn']);
    }
}
