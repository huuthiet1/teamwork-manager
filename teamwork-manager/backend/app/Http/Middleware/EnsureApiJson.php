<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EnsureApiJson
{
    public function handle(Request $request, Closure $next)
    {
        if (!$request->expectsJson()) {
            return response()->json([
                'message' => 'API chỉ hỗ trợ JSON'
            ], 406);
        }

        return $next($request);
    }
}
