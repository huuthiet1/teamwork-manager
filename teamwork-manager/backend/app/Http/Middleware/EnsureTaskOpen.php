<?php

namespace App\Http\Middleware;

use App\Models\Task;
use Closure;
use Illuminate\Http\Request;

class EnsureTaskOpen
{
    public function handle(Request $request, Closure $next)
    {
        $taskParam = $request->route('task');

        $task = $taskParam instanceof Task
            ? $taskParam
            : Task::find($taskParam);

        if (!$task) {
            abort(404, 'Nhiệm vụ không tồn tại.');
        }

        if ($task->status !== 'doing') {
            abort(403, 'Nhiệm vụ đã bị đóng hoặc huỷ.');
        }

        return $next($request);
    }
}
