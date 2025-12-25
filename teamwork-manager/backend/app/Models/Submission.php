<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
class Submission extends Model
{
    protected $fillable = [
        'task_id',
        'user_id',
        'note',
        'feedback',
        'graded_by'
    ];

    public function versions()
    {
        return $this->hasMany(SubmissionVersion::class);
    }

    public function task()
    {
        return $this->belongsTo(Task::class);
    }
}
