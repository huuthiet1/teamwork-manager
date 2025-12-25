<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GroupInvite extends Model
{
    protected $table = 'group_invites';

    public $timestamps = false;

    protected $fillable = [
        'group_id',
        'otp_code',
        'created_by',
        'expires_at',
        'used_at',
        'used_by',
        'revoked_at',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'used_at' => 'datetime',
        'revoked_at' => 'datetime',
    ];

    /* ================= RELATIONS ================= */

    public function group()
    {
        return $this->belongsTo(WorkGroup::class, 'group_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function usedBy()
    {
        return $this->belongsTo(User::class, 'used_by');
    }

    /* ================= HELPERS ================= */

    public function isValid(): bool
    {
        return is_null($this->used_at)
            && is_null($this->revoked_at)
            && now()->lessThan($this->expires_at);
    }
}
