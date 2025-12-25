<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MessageRead extends Model
{
    protected $table = 'message_reads';
    public $timestamps = false;

    protected $fillable = [
        'group_id',
        'user_id',
        'last_read_message_id',
        'last_read_at'
    ];

    protected $casts = [
        'last_read_at' => 'datetime',
    ];

    public function group()
    {
        return $this->belongsTo(WorkGroup::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
