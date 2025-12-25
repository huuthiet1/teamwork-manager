<?php

namespace App\Http\Middleware;

use App\Models\WorkGroup;
use Closure;
use Illuminate\Http\Request;

class EnsureGroupExists
{
    public function handle(Request $request, Closure $next)
    {
        $groupId = session('current_group_id');

        if (!$groupId) {
            return redirect()
                ->route('dashboard')
                ->withErrors('Bạn chưa chọn nhóm.');
        }

        $group = WorkGroup::find($groupId);

        if (!$group || $group->is_deleted) {
            session()->forget('current_group_id');

            return redirect()
                ->route('dashboard')
                ->withErrors('Nhóm không tồn tại hoặc đã bị đóng.');
        }

        // Set vào request để controller dùng lại
        $request->attributes->set('currentGroup', $group);

        return $next($request);
    }
}
