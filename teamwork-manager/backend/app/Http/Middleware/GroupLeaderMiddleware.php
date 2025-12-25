<?php

namespace App\Http\Middleware;

use App\Models\WorkGroup;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GroupLeaderMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $groupParam = $request->route('group');

        $group = $groupParam instanceof WorkGroup
            ? $groupParam
            : WorkGroup::find($groupParam);

        if (!$group) {
            return response()->json([
                'message' => 'Không xác định được nhóm'
            ], 400);
        }

        if ($group->is_deleted) {
            return response()->json([
                'message' => 'Nhóm đã bị đóng'
            ], 403);
        }

        if ((int) $group->leader_id !== (int) Auth::id()) {
            return response()->json([
                'message' => 'Chỉ leader mới có quyền thực hiện thao tác'
            ], 403);
        }

        // Cho controller dùng lại
        $request->attributes->set('currentGroup', $group);

        return $next($request);
    }
}
