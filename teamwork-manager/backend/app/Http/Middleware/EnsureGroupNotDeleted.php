<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\WorkGroup;

class EnsureGroupNotDeleted
{
    public function handle(Request $request, Closure $next)
    {
        $group =
            $request->attributes->get('currentGroup')
            ?? ($request->route('group') instanceof WorkGroup
                ? $request->route('group')
                : WorkGroup::find($request->route('group')));

        if ($group && $group->is_deleted) {
            return response()->json([
                'message' => 'Nhóm đã bị đóng'
            ], 403);
        }

        return $next($request);
    }
}
