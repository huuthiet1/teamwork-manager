<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WorkGroup extends Model
{
    protected $table = 'work_groups';

    protected $fillable = [
        'name',
        'description',
        'group_code',
        'leader_id',
        'teacher_email',
        'is_deleted',
    ];

    protected $casts = [
        'is_deleted' => 'boolean',
    ];

    /* ================= RELATIONS ================= */

    // Leader của nhóm
    public function leader()
    {
        return $this->belongsTo(User::class, 'leader_id');
    }

    // Member active
    public function members()
    {
        return $this->belongsToMany(User::class, 'group_members')
            ->withPivot(['role', 'is_active', 'joined_at'])
            ->wherePivot('is_active', 1);
    }

    // Tất cả member (kể cả inactive)
    public function allMembers()
    {
        return $this->hasMany(GroupMember::class, 'group_id');
    }

    // OTP mời
    public function invites()
    {
        return $this->hasMany(GroupInvite::class, 'group_id');
    }

    // Request xin vào nhóm
    public function joinRequests()
    {
        return $this->hasMany(GroupJoinRequest::class, 'group_id');
    }

    // Tasks (để dùng dashboard sau)
    public function tasks()
    {
        return $this->hasMany(Task::class, 'group_id');
    }
}
