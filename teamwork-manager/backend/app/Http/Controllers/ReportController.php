<?php

namespace App\Http\Controllers;

use App\Models\WorkGroup;
use App\Models\GroupMember;
use App\Models\Task;
use App\Models\Report;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;

class ReportController extends Controller
{
    /**
     * Export PDF báo cáo nhóm
     */
    public function export(Request $request)
    {
        $request->validate([
            'group_id' => 'required|integer'
        ]);

        /** @var WorkGroup $group */
        $group = WorkGroup::with([
                'tasks.assignments.user',
                'tasks.subtasks',
                'members'
            ])
            ->find($request->group_id);

        // 1️⃣ Check group tồn tại & chưa bị đóng
        if (!$group || $group->is_deleted) {
            return response()->json(['message' => 'Nhóm không tồn tại hoặc đã bị đóng'], 403);
        }

        // 2️⃣ Check member
        $member = GroupMember::where([
            'group_id' => $group->id,
            'user_id'  => Auth::id(),
            'is_active'=> 1
        ])->first();

        if (!$member) {
            return response()->json(['message' => 'Không có quyền truy cập'], 403);
        }

        // 3️⃣ Check quyền export (leader)
        if ($group->leader_id !== Auth::id()) {
            return response()->json(['message' => 'Chỉ leader mới được xuất báo cáo'], 403);
        }

        // 4️⃣ Chuẩn bị dữ liệu báo cáo
        $tasks = $group->tasks->map(function (Task $task) {
            return [
                'title' => $task->title,
                'deadline' => $task->deadline,
                'difficulty' => $task->difficulty,
                'status' => $task->status,
                'assignments' => $task->assignments->map(function ($a) {
                    return [
                        'user' => $a->user->name,
                        'status' => $a->status,
                        'done_at' => $a->done_at,
                    ];
                })
            ];
        });

        // 5️⃣ Generate PDF
        $pdf = Pdf::loadView('reports.group', [
            'group' => $group,
            'tasks' => $tasks,
            'exported_by' => Auth::user()->name,
            'exported_at' => now(),
        ]);

        // (OPTIONAL) 6️⃣ Lưu lịch sử report (nếu bạn tạo bảng reports)
        /*
        $path = 'reports/group_'.$group->id.'_'.time().'.pdf';
        Storage::disk('public')->put($path, $pdf->output());

        Report::create([
            'group_id' => $group->id,
            'pdf_path' => $path,
            'send_status' => 'generated',
        ]);
        */

        return $pdf->download(
            'report_group_'.$group->id.'_'.now()->format('Ymd_His').'.pdf'
        );
    }
}
