<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Submission;
use App\Models\SubmissionVersion;
use App\Models\Task;
use App\Models\User;

class SubmissionSeeder extends Seeder
{
    public function run()
    {
        Submission::truncate();
        SubmissionVersion::truncate();

        $task = Task::first();
        $user = User::where('email', 'member1@test.com')->first();

        $submission = Submission::create([
            'task_id' => $task->id,
            'user_id' => $user->id,
            'note' => 'Bài nộp test'
        ]);

        SubmissionVersion::create([
            'submission_id' => $submission->id,
            'version_no' => 1,
            'content' => 'Nội dung bài nộp lần 1'
        ]);
    }
}
