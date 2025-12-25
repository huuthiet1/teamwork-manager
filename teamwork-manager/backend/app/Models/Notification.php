<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
class Notification extends Model
{
    protected $fillable = [
        'user_id',
        'group_id',
        'ref_type',
        'ref_id',
        'title',
        'content',
        'priority',
        'is_read'
    ];

    protected $casts = [
        'is_read' => 'boolean',
    ];
}
