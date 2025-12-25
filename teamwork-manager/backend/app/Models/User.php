<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password_hash',
        'avatar_path',
        'is_active'
    ];

    protected $hidden = [
        'password_hash',
        'remember_token',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'last_active_at' => 'datetime',
    ];

    // 🔥 FIX QUAN TRỌNG NHẤT
    public function getAuthPassword()
    {
        return $this->password_hash;
    }

    /* ================= Relations ================= */

  public function groups()
{
    return $this->belongsToMany(
        WorkGroup::class,
        'group_members',
        'user_id',
        'group_id'
    );
}


    public function ledGroups()
    {
        return $this->hasMany(WorkGroup::class, 'leader_id');
    }
     // Request xin vào nhóm
    public function groupJoinRequests()
    {
        return $this->hasMany(GroupJoinRequest::class, 'user_id');
    }
    public function tasksAssigned()
    {
        return $this->hasMany(TaskAssignment::class);
    }

    public function submissions()
    {
        return $this->hasMany(Submission::class);
    }

    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }
}
