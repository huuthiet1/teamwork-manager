<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\TaskAssignment;
use App\Models\GroupMember;
use App\Models\WorkGroup;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TaskController extends Controller
{
    /**
     * Danh sách task theo group
     */
    public function index(Request $request)
    {
        $request->validate([
            'group_id' => 'required|integer'
        ]);

        $group = WorkGroup::find($request->group_id);

        if (!$group || $group->is_deleted) {
            return response()->json(['message' => 'Nhóm không tồn tại hoặc đã bị đóng'], 403);
        }

        $member = GroupMember::where([
            'group_id' => $group->id,
            'user_id'  => Auth::id(),
            'is_active'=> 1
        ])->first();

        if (!$member) {
            return response()->json(['message' => 'Không có quyền truy cập'], 403);
        }

        $tasks = Task::where('group_id', $group->id)
            ->orderBy('deadline')
            ->get();

        return response()->json([
            'tasks' => $tasks
        ]);
    }

    /**
     * Tạo task (CHỈ LEADER)
     */
    public function store(Request $request)
    {
        $request->validate([
            'group_id'  => 'required|integer',
            'title'     => 'required|string|max:255',
            'description'=> 'nullable|string',
            'difficulty'=> 'nullable|integer|min:1|max:5',
          'deadline' => 'required|date|after:' . now()->toDateTimeString(),


            'assignees' => 'required|array|min:1',
        ]);

        $group = WorkGroup::find($request->group_id);

        if (!$group || $group->is_deleted) {
            return response()->json(['message' => 'Nhóm không tồn tại hoặc đã bị đóng'], 403);
        }

        if ($group->leader_id !== Auth::id()) {
            return response()->json(['message' => 'Chỉ leader mới được tạo nhiệm vụ'], 403);
        }

        // Validate assignees thuộc group
        $validUserIds = GroupMember::where('group_id', $group->id)
            ->where('is_active', 1)
            ->pluck('user_id')
            ->toArray();

        foreach ($request->assignees as $uid) {
            if (!in_array($uid, $validUserIds)) {
                return response()->json([
                    'message' => 'Có thành viên không thuộc nhóm'
                ], 400);
            }
        }

        $task = DB::transaction(function () use ($request, $group) {

            $task = Task::create([
                'group_id'   => $group->id,
                'title'      => $request->title,
                'description'=> $request->description,
                'difficulty' => $request->difficulty ?? 1,
                'deadline'   => $request->deadline,
                'created_by' => Auth::id(),
                'status'     => 'doing'
            ]);

            foreach ($request->assignees as $uid) {
                TaskAssignment::create([
                    'task_id'     => $task->id,
                    'user_id'     => $uid,
                    'assigned_by'=> Auth::id(),
                ]);
            }

            return $task;
        });

        return response()->json([
            'message' => 'Tạo nhiệm vụ thành công',
            'task' => $task
        ], 201);
    }

    /**
     * Cập nhật task (CHỈ LEADER)
     */
    public function update(Request $request, Task $task)
    {
        if ($task->group->is_deleted) {
            return response()->json(['message' => 'Nhóm đã bị đóng'], 403);
        }

        if ($task->group->leader_id !== Auth::id()) {
            return response()->json(['message' => 'Chỉ leader mới được sửa nhiệm vụ'], 403);
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'difficulty' => 'required|integer|min:1|max:5',
            'deadline' => 'required|date|after:' . now()->toDateTimeString(),

        ]);

        $task->update([
            'title'       => $request->title,
            'description' => $request->description,
            'difficulty'  => $request->difficulty,
            'deadline'    => $request->deadline,
            'updated_by'  => Auth::id(),
        ]);

        return response()->json([
            'message' => 'Cập nhật nhiệm vụ thành công',
            'task' => $task
        ]);
    }

    /**
     * Huỷ task (SOFT – đổi status)
     */
    public function destroy(Request $request, Task $task)
    {
        if ($task->group->is_deleted) {
            return response()->json(['message' => 'Nhóm đã bị đóng'], 403);
        }

        if ($task->group->leader_id !== Auth::id()) {
            return response()->json(['message' => 'Chỉ leader mới được huỷ nhiệm vụ'], 403);
        }

        $task->update([
            'status' => 'cancelled',
            'cancel_reason' => $request->reason ?? 'Leader huỷ nhiệm vụ'
        ]);

        return response()->json([
            'message' => 'Đã huỷ nhiệm vụ'
        ]);
    }
}
