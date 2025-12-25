<?php

namespace App\Http\Middleware;

use App\Models\Task;
use App\Models\TaskAssignment;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EnsureTaskAssigned
{
    public function handle(Request $request, Closure $next)
    {
        $userId = Auth::id();
        if (!$userId) {
            abort(401, 'Chưa đăng nhập.');
        }

        // Route model binding có thể là Task hoặc id
        $taskParam = $request->route('task');
        $taskId = $taskParam instanceof Task ? $taskParam->id : $taskParam;

        if (!$taskId || !Task::where('id', $taskId)->exists()) {
            abort(404, 'Nhiệm vụ không tồn tại.');
        }

        $assigned = TaskAssignment::where('task_id', $taskId)
            ->where('user_id', $userId)
            ->exists();

        if (!$assigned) {
            abort(403, 'Bạn không được giao nhiệm vụ này.');
        }

        return $next($request);
    }
}
