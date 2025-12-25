<?php

namespace App\Http\Middleware;

use App\Models\GroupMember;
use App\Models\WorkGroup;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EnsureGroupRole
{
    /**
     * Usage:
     *  ->middleware('group.role:leader')
     *  ->middleware('group.role:member')
     */
    public function handle(Request $request, Closure $next, string $role)
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

        $member = GroupMember::where([
            'group_id'  => $group->id,
            'user_id'   => Auth::id(),
            'is_active' => 1,
        ])->first();

        if (!$member) {
            return response()->json([
                'message' => 'Bạn không phải là thành viên của nhóm'
            ], 403);
        }

        if ($role === 'leader' && $member->role !== 'leader') {
            return response()->json([
                'message' => 'Chỉ leader mới có quyền thực hiện thao tác'
            ], 403);
        }

        // Cho controller dùng lại
        $request->attributes->set('currentGroup', $group);
        $request->attributes->set('groupRole', $member->role);

        return $next($request);
    }
}
