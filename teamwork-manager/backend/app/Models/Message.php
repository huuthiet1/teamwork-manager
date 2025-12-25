<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
class Message extends Model
{
    protected $fillable = [
        'group_id',
        'user_id',
        'type',
        'content',
        'is_deleted'
    ];

    protected $casts = [
        'is_deleted' => 'boolean',
    ];
}
