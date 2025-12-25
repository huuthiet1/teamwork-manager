<?php

namespace App\Http\Controllers;

use App\Models\Submission;
use App\Models\SubmissionVersion;
use App\Models\SubmissionFile;
use App\Models\Task;
use App\Models\TaskAssignment;
use App\Models\GroupMember;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class SubmissionController extends Controller
{
    /**
     * Nộp bài / cập nhật bài nộp
     */
    public function store(Request $request, Task $task)
    {
        // 1️⃣ Check group bị đóng
        if ($task->group->is_deleted) {
            return response()->json(['message' => 'Nhóm đã bị đóng'], 403);
        }

        // 2️⃣ Check user là member active của group
        $member = GroupMember::where([
            'group_id' => $task->group_id,
            'user_id'  => Auth::id(),
            'is_active'=> 1
        ])->first();

        if (!$member) {
            return response()->json(['message' => 'Không có quyền thao tác'], 403);
        }

        // 3️⃣ Check user được giao task
        $assignment = TaskAssignment::where([
            'task_id' => $task->id,
            'user_id' => Auth::id(),
        ])->first();

        if (!$assignment) {
            return response()->json(['message' => 'Bạn không được giao nhiệm vụ này'], 403);
        }

        $request->validate([
            'note' => 'nullable|string',
            'files.*' => 'nullable|file|max:5120|mimes:jpg,jpeg,png,pdf,docx,mp3,wav'
        ]);

        $result = DB::transaction(function () use ($request, $task) {

            // 4️⃣ Create / get submission
            $submission = Submission::firstOrCreate([
                'task_id' => $task->id,
                'user_id' => Auth::id(),
            ]);

            // 5️⃣ Tăng version
            $nextVersion = ($submission->versions()->max('version_no') ?? 0) + 1;

            $version = SubmissionVersion::create([
                'submission_id' => $submission->id,
                'version_no'    => $nextVersion,
                'content'       => $request->note,
                'is_late'       => now()->gt($task->deadline),
            ]);

            // 6️⃣ Upload file minh chứng (nếu có)
            if ($request->hasFile('files')) {
                foreach ($request->file('files') as $file) {
                    $path = $file->store('submissions', 'public');

                    SubmissionFile::create([
                        'submission_version_id' => $version->id,
                        'file_path' => $path,
                        'file_type' => $this->detectFileType($file->getMimeType()),
                    ]);
                }
            }

            return $version;
        });

        return response()->json([
            'message' => 'Nộp bài thành công',
            'version' => $result
        ], 201);
    }

    /**
     * Xác định loại file
     */
    private function detectFileType(string $mime): string
    {
        if (str_starts_with($mime, 'image/')) return 'image';
        if (str_starts_with($mime, 'audio/')) return 'audio';
        return 'file';
    }
}
