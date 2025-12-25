<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SubmissionFile extends Model
{
    protected $table = 'submission_files';

    // ❗ Bảng này KHÔNG dùng created_at / updated_at
    public $timestamps = false;

    protected $fillable = [
        'submission_version_id',
        'file_path',
        'file_type',
        'uploaded_at'
    ];

    protected $casts = [
        'uploaded_at' => 'datetime',
    ];

    /* ================= Relations ================= */

    public function version()
    {
        return $this->belongsTo(
            SubmissionVersion::class,
            'submission_version_id'
        );
    }
}
