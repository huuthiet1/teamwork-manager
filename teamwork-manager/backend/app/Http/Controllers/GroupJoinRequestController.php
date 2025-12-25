<?php

namespace App\Http\Controllers;

use App\Models\GroupJoinRequest;
use App\Models\GroupMember;
use App\Models\WorkGroup;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class GroupJoinRequestController extends Controller
{
    /**
     * Gửi request join group
     */
    public function store(WorkGroup $group)
    {
        if ($group->is_deleted) {
            return response()->json(['message' => 'Nhóm đã bị đóng'], 403);
        }

        if (GroupMember::where([
            'group_id' => $group->id,
            'user_id'  => Auth::id(),
            'is_active'=> 1
        ])->exists()) {
            return response()->json(['message' => 'Bạn đã ở trong nhóm'], 400);
        }

        // Nếu từng rejected thì cho gửi lại bằng cách set pending
        $req = GroupJoinRequest::where([
            'group_id' => $group->id,
            'user_id'  => Auth::id(),
        ])->first();

        if ($req && $req->status === 'approved') {
            return response()->json(['message' => 'Bạn đã được duyệt trước đó'], 400);
        }

        if ($req) {
            $req->update([
                'status' => 'pending',
                'reviewed_by' => null,
                'reviewed_at' => null,
            ]);
        } else {
            GroupJoinRequest::create([
                'group_id' => $group->id,
                'user_id'  => Auth::id(),
                'status'   => 'pending',
            ]);
        }

        return response()->json(['message' => 'Đã gửi yêu cầu']);
    }

    /**
     * Danh sách request pending (leader)
     */
    public function index(WorkGroup $group)
    {
        if ($group->is_deleted) {
            return response()->json(['message' => 'Nhóm đã bị đóng'], 403);
        }

        if ($group->leader_id !== Auth::id()) {
            return response()->json(['message' => 'Không có quyền'], 403);
        }

        return response()->json([
            'requests' => GroupJoinRequest::where('group_id', $group->id)
                ->where('status', 'pending')
                ->orderByDesc('id')
                ->get()
        ]);
    }

    /**
     * Approve request
     */
    public function approve(GroupJoinRequest $requestJoin)
    {
        $group = $requestJoin->group;

        if (!$group || $group->is_deleted) {
            return response()->json(['message' => 'Nhóm đã bị đóng'], 403);
        }

        if ($group->leader_id !== Auth::id()) {
            return response()->json(['message' => 'Không có quyền'], 403);
        }

        if ($requestJoin->status !== 'pending') {
            return response()->json(['message' => 'Request đã xử lý'], 400);
        }

        DB::transaction(function () use ($requestJoin) {
            GroupMember::updateOrCreate(
                [
                    'group_id' => $requestJoin->group_id,
                    'user_id'  => $requestJoin->user_id,
                ],
                [
                    'role'      => 'member',
                    'is_active' => 1,
                    'joined_at' => now(),
                    'last_interaction_at' => now(),
                ]
            );

            $requestJoin->update([
                'status'      => 'approved',
                'reviewed_by' => Auth::id(),
                'reviewed_at' => now()
            ]);
        });

        return response()->json(['message' => 'Đã duyệt']);
    }

    /**
     * Reject request
     */
    public function reject(GroupJoinRequest $requestJoin)
    {
        $group = $requestJoin->group;

        if (!$group || $group->is_deleted) {
            return response()->json(['message' => 'Nhóm đã bị đóng'], 403);
        }

        if ($group->leader_id !== Auth::id()) {
            return response()->json(['message' => 'Không có quyền'], 403);
        }

        if ($requestJoin->status !== 'pending') {
            return response()->json(['message' => 'Request đã xử lý'], 400);
        }

        $requestJoin->update([
            'status'      => 'rejected',
            'reviewed_by' => Auth::id(),
            'reviewed_at' => now()
        ]);

        return response()->json(['message' => 'Đã từ chối']);
    }
}
