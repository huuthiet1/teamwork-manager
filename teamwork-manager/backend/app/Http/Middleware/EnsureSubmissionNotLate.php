<?php

namespace App\Http\Middleware;

use App\Models\Submission;
use Closure;
use Illuminate\Http\Request;

class EnsureSubmissionNotLate
{
    public function handle(Request $request, Closure $next)
    {
        $submissionParam = $request->route('submission');

        $submission = $submissionParam instanceof Submission
            ? $submissionParam
            : Submission::find($submissionParam);

        if (!$submission) {
            abort(404, 'Bài nộp không tồn tại.');
        }

        $latest = $submission->versions()
            ->orderByDesc('version_no')
            ->first();

        if ($latest && $latest->is_late) {
            abort(403, 'Bài nộp đã trễ hạn.');
        }

        return $next($request);
    }
}
