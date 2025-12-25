<?php

namespace App\Http\Middleware;

use App\Models\GroupMember;
use App\Models\WorkGroup;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GroupMemberMiddleware
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

        $isMember = GroupMember::where([
            'group_id'  => $group->id,
            'user_id'   => Auth::id(),
            'is_active' => 1,
        ])->exists();

        if (!$isMember) {
            return response()->json([
                'message' => 'Bạn không phải là thành viên của nhóm này'
            ], 403);
        }

        // Cho controller dùng lại
        $request->attributes->set('currentGroup', $group);

        return $next($request);
    }
}
