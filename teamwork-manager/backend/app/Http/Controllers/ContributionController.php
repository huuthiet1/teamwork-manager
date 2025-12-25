<?php

namespace App\Http\Controllers;

use App\Models\TaskAssignment;
use App\Models\WorkGroup;
use App\Models\GroupMember;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ContributionController extends Controller
{
    /**
     * Xem điểm đóng góp của group
     */
    public function show(Request $request)
    {
        $request->validate([
            'group_id' => 'required|integer'
        ]);

        $group = WorkGroup::find($request->group_id);

        if (!$group || $group->is_deleted) {
            return response()->json(['message' => 'Nhóm không tồn tại'], 403);
        }

        if ($group->leader_id !== Auth::id()) {
            return response()->json(['message' => 'Chỉ leader được xem'], 403);
        }

        $members = GroupMember::where('group_id', $group->id)
            ->where('is_active', 1)
            ->get();

        $scores = [];

        foreach ($members as $m) {
            $assignments = TaskAssignment::with('task')
                ->where('user_id', $m->user_id)
                ->whereHas('task', function ($q) use ($group) {
                    $q->where('group_id', $group->id);
                })
                ->get();

            $score = 0;

            foreach ($assignments as $a) {
                if ($a->status === 'done') {
                    $score += 10 + ($a->task->difficulty * 2);
                } elseif ($a->status === 'late') {
                    $score -= 5;
                }
            }

            $scores[] = [
                'user_id' => $m->user_id,
                'score' => max($score, 0)
            ];
        }

        return response()->json([
            'scores' => $scores
        ]);
    }
}
