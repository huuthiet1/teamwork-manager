<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GroupJoinRequest extends Model
{
    protected $table = 'group_join_requests';

    public $timestamps = false;

    protected $fillable = [
        'group_id',
        'user_id',
        'invite_id',
        'status',
        'reviewed_by',
        'reviewed_at',
    ];

    protected $casts = [
        'reviewed_at' => 'datetime',
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

    public function invite()
    {
        return $this->belongsTo(GroupInvite::class, 'invite_id');
    }

    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    /* ================= HELPERS ================= */

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }
}
