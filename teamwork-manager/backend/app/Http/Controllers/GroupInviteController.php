<?php

namespace App\Http\Controllers;

use App\Models\GroupInvite;
use App\Models\GroupMember;
use App\Models\WorkGroup;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class GroupInviteController extends Controller
{
    /**
     * Leader tạo OTP
     */
    public function generate(WorkGroup $group)
    {
        if ($group->is_deleted) {
            return response()->json(['message' => 'Nhóm đã bị đóng'], 403);
        }

        if ($group->leader_id !== Auth::id()) {
            return response()->json(['message' => 'Không có quyền'], 403);
        }

        $otp = (string) random_int(100000, 999999);

        $invite = GroupInvite::create([
            'group_id'   => $group->id,
            'otp_code'   => $otp,
            'created_by' => Auth::id(),
            'expires_at' => now()->addMinutes(10),
        ]);

        return response()->json([
            'invite' => $invite
        ]);
    }

    /**
     * Join group bằng OTP
     */
    public function join(Request $request)
    {
        $request->validate([
            'otp_code' => 'required|string|size:6'
        ]);

        // Chỉ lấy OTP còn hạn + chưa dùng + chưa revoke
        $invite = GroupInvite::where('otp_code', $request->otp_code)
            ->whereNull('used_at')
            ->whereNull('revoked_at')
            ->where('expires_at', '>=', now())
            ->orderByDesc('id')
            ->first();

        if (!$invite) {
            return response()->json(['message' => 'OTP không đúng hoặc đã hết hạn/đã dùng'], 400);
        }

        $group = WorkGroup::find($invite->group_id);
        if (!$group || $group->is_deleted) {
            return response()->json(['message' => 'Nhóm đã bị đóng hoặc không tồn tại'], 403);
        }

        if (GroupMember::where([
            'group_id' => $invite->group_id,
            'user_id'  => Auth::id(),
            'is_active'=> 1
        ])->exists()) {
            return response()->json(['message' => 'Bạn đã ở trong nhóm'], 400);
        }

        DB::transaction(function () use ($invite) {
            GroupMember::updateOrCreate(
                [
                    'group_id' => $invite->group_id,
                    'user_id'  => Auth::id(),
                ],
                [
                    'role'      => 'member',
                    'is_active' => 1,
                    'joined_at' => now(),
                    'last_interaction_at' => now(),
                ]
            );

            $invite->update([
                'used_at' => now(),
                'used_by' => Auth::id(),
            ]);
        });

        return response()->json(['message' => 'Tham gia nhóm thành công']);
    }
}
