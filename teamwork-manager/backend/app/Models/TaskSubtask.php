<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TaskSubtask extends Model
{
    protected $table = 'task_subtasks';

    protected $fillable = [
        'task_id',
        'title',
        'is_done'
    ];

    protected $casts = [
        'is_done' => 'boolean',
    ];

    /* ================= Relations ================= */

    public function task()
    {
        return $this->belongsTo(Task::class, 'task_id');
    }
}
