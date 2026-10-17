<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProjectEnquiry extends Model
{
    protected $fillable = [
        'name',
        'email',
        'phone',
        'organization',
        'service',
        'industry',
        'languages',
        'other_language',
        'volume',
        'deadline',
        'message',
        'attachment_path',
        'attachment_name',
        'consented_at',
    ];

    protected function casts(): array
    {
        return [
            'languages' => 'array',
            'consented_at' => 'datetime',
            'read_at' => 'datetime',
        ];
    }
}
