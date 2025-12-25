<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GroupMember extends Model
{
    protected $table = 'group_members';

    public $timestamps = false;

    protected $fillable = [
        'group_id',
        'user_id',
        'role',
        'is_active',
        'joined_at',
        'last_interaction_at',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'joined_at' => 'datetime',
        'last_interaction_at' => 'datetime',
    ];

    /* ================= RELATIONS ================= */

    public function group()
    {
        return $this->belongsTo(WorkGroup::class, 'group_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
