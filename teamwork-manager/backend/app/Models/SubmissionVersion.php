<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SubmissionVersion extends Model
{
    protected $fillable = [
        'submission_id',
        'version_no',
        'content',
        'is_late'
    ];

    protected $casts = [
        'is_late' => 'boolean',
    ];

    public function files()
    {
        return $this->hasMany(SubmissionFile::class);
    }
}
