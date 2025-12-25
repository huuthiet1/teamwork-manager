<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    /**
     * Danh sách notification (có filter + pagination)
     */
    public function index(Request $request)
    {
        $query = Notification::where('user_id', Auth::id());

        // Optional: filter theo group
        if ($request->filled('group_id')) {
            $query->where('group_id', $request->group_id);
        }

        $notifications = $query
            ->orderByRaw("
                CASE priority
                    WHEN 'high' THEN 1
                    WHEN 'normal' THEN 2
                    WHEN 'low' THEN 3
                END
            ")
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return response()->json($notifications);
    }

    /**
     * Đánh dấu 1 notification đã đọc
     */
    public function read($id)
    {
        $notification = Notification::where([
            'id' => $id,
            'user_id' => Auth::id()
        ])->first();

        if (!$notification) {
            return response()->json([
                'message' => 'Notification không tồn tại'
            ], 404);
        }

        if (!$notification->is_read) {
            $notification->update([
                'is_read' => 1,
                'read_at' => now()
            ]);
        }

        return response()->json([
            'message' => 'Đã đánh dấu đã đọc'
        ]);
    }

    /**
     * Đánh dấu toàn bộ notification đã đọc
     */
    public function markAllAsRead(Request $request)
    {
        $query = Notification::where('user_id', Auth::id())
            ->where('is_read', 0);

        // Optional filter theo group
        if ($request->filled('group_id')) {
            $query->where('group_id', $request->group_id);
        }

        $query->update([
            'is_read' => 1,
            'read_at' => now()
        ]);

        return response()->json([
            'message' => 'Đã đánh dấu tất cả là đã đọc'
        ]);
    }

    /**
     * Số notification chưa đọc (badge)
     */
    public function unreadCount(Request $request)
    {
        $query = Notification::where('user_id', Auth::id())
            ->where('is_read', 0);

        if ($request->filled('group_id')) {
            $query->where('group_id', $request->group_id);
        }

        return response()->json([
            'unread_count' => $query->count()
        ]);
    }
}
