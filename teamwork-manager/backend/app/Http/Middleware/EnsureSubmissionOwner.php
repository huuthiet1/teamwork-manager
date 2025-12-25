<?php

namespace App\Http\Middleware;

use App\Models\GroupMember;
use App\Models\Submission;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EnsureSubmissionOwner
{
    public function handle(Request $request, Closure $next)
    {
        $userId = Auth::id();
        if (!$userId) {
            abort(401, 'Chưa đăng nhập.');
        }

        // Route model binding có thể là Submission hoặc id
        $submissionParam = $request->route('submission');

        $submission = $submissionParam instanceof Submission
            ? $submissionParam
            : Submission::find($submissionParam);

        if (!$submission) {
            abort(404, 'Bài nộp không tồn tại.');
        }

        // Chủ bài nộp
        if ((int) $submission->user_id === (int) $userId) {
            return $next($request);
        }

        // Leader của group được xem/chấm
        // Cần biết group_id -> thường đi theo submission->task->group_id
        $submission->loadMissing('task:id,group_id');

        $groupId = $submission->task?->group_id;
        if (!$groupId) {
            abort(400, 'Không xác định được nhóm của bài nộp.');
        }

        $isLeader = GroupMember::where([
            'group_id'  => $groupId,
            'user_id'   => $userId,
            'is_active' => 1,
            'role'      => 'leader',
        ])->exists();

        if ($isLeader) {
            return $next($request);
        }

        abort(403, 'Bạn không có quyền truy cập bài nộp này.');
    }
}
