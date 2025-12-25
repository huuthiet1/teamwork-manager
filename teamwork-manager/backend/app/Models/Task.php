<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    protected $fillable = [
        'group_id',
        'title',
        'description',
        'difficulty',
        'deadline',
        'status',
        'created_by',
        'updated_by'
    ];

    protected $casts = [
        'deadline' => 'datetime',
        'completed_at' => 'datetime',
    ];

    /* ================= Relations ================= */

    public function group()
    {
        return $this->belongsTo(WorkGroup::class);
    }

    public function assignments()
    {
        return $this->hasMany(TaskAssignment::class);
    }

    public function subtasks()
    {
        return $this->hasMany(TaskSubtask::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
