<?php

namespace App\Http\Middleware;

use App\Models\WorkGroup;
use Closure;
use Illuminate\Http\Request;

class GroupContextMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $groupId = session('current_group_id');

        if (!$groupId) {
            return response()->json([
                'message' => 'Bạn chưa chọn nhóm'
            ], 400);
        }

        $group = WorkGroup::find($groupId);

        if (!$group || $group->is_deleted) {
            session()->forget('current_group_id');

            return response()->json([
                'message' => 'Nhóm không tồn tại hoặc đã bị đóng'
            ], 403);
        }

        // Set vào request để controller dùng lại
        $request->attributes->set('currentGroup', $group);

        return $next($request);
    }
}
