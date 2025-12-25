<?php

namespace App\Http\Controllers;

use App\Models\WorkGroup;
use App\Models\GroupMember;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class GroupController extends Controller
{
    /**
     * Danh sách nhóm của user (chỉ nhóm còn hoạt động + membership active)
     */
    public function index()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $groups = $user->groups()
            ->where('work_groups.is_deleted', 0)
            ->wherePivot('is_active', 1)
            ->withPivot('role', 'last_interaction_at')
            ->orderByDesc('group_members.last_interaction_at')
            ->get();

        return response()->json([
            'groups' => $groups
        ]);
    }

    /**
     * Tạo nhóm
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:150',
            'description' => 'nullable|string'
        ]);

        $group = DB::transaction(function () use ($request) {

            // generate unique group_code
            do {
                $code = strtoupper(Str::random(6));
            } while (WorkGroup::where('group_code', $code)->exists());

            $group = WorkGroup::create([
                'name'        => $request->name,
                'description' => $request->description,
                'group_code'  => $code,
                'leader_id'   => Auth::id(),
                'is_deleted'  => 0,
            ]);

            // add leader membership
            GroupMember::create([
                'group_id'  => $group->id,
                'user_id'   => Auth::id(),
                'role'      => 'leader',
                'is_active' => 1,
                'joined_at' => now(),
                'last_interaction_at' => now(),
            ]);

            return $group;
        });

        return response()->json([
            'group' => $group
        ], 201);
    }

    /**
     * Xem chi tiết nhóm (chỉ member active)
     */
    public function show(WorkGroup $group)
    {
        if ($group->is_deleted) {
            return response()->json(['message' => 'Nhóm đã bị đóng'], 403);
        }

        $member = GroupMember::where([
            'group_id' => $group->id,
            'user_id'  => Auth::id(),
            'is_active'=> 1
        ])->first();

        if (!$member) {
            return response()->json(['message' => 'Không có quyền'], 403);
        }

        $activeMembers = $group->members()
            ->wherePivot('is_active', 1)
            ->withPivot('role', 'joined_at', 'last_interaction_at')
            ->get();

        return response()->json([
            'group' => $group,
            'members' => $activeMembers,
            'stats' => [
                'total_members'  => $group->members()->count(),
                'active_members' => $group->members()->wherePivot('is_active', 1)->count(),
            ]
        ]);
    }

    /**
     * Đóng nhóm (soft delete)
     */
    public function destroy(WorkGroup $group)
    {
        if ($group->leader_id !== Auth::id()) {
            return response()->json(['message' => 'Chỉ leader mới có quyền'], 403);
        }

        if ($group->is_deleted) {
            return response()->json(['message' => 'Nhóm đã bị đóng trước đó'], 400);
        }

        $group->update(['is_deleted' => 1]);

        return response()->json(['message' => 'Nhóm đã được đóng']);
    }

    /**
     * Chuyển quyền leader
     */
    public function transferLeader(Request $request, WorkGroup $group)
    {
        if ($group->is_deleted) {
            return response()->json(['message' => 'Nhóm đã bị đóng'], 403);
        }

        if ($group->leader_id !== Auth::id()) {
            return response()->json(['message' => 'Không có quyền'], 403);
        }

        $request->validate([
            'new_leader_id' => 'required|integer|different:' . Auth::id(),
        ]);

        $newLeader = GroupMember::where([
            'group_id' => $group->id,
            'user_id'  => $request->new_leader_id,
            'is_active'=> 1
        ])->first();

        if (!$newLeader) {
            return response()->json(['message' => 'Người này không thuộc nhóm hoặc đã inactive'], 400);
        }

        DB::transaction(function () use ($group, $newLeader) {
            // current leader -> member
            GroupMember::where([
                'group_id' => $group->id,
                'user_id'  => Auth::id(),
            ])->update(['role' => 'member']);

            // new leader -> leader
            $newLeader->update(['role' => 'leader']);

            // source of truth
            $group->update(['leader_id' => $newLeader->user_id]);
        });

        return response()->json(['message' => 'Đã chuyển quyền leader']);
    }
}
