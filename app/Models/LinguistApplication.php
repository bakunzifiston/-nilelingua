<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LinguistApplication extends Model
{
    protected $fillable = [
        'name',
        'email',
        'phone',
        'location',
        'native_languages',
        'language_pairs',
        'expertise',
        'tools',
        'cv_path',
        'cv_name',
        'consented_at',
    ];

    protected function casts(): array
    {
        return [
            'native_languages' => 'array',
            'tools' => 'array',
            'consented_at' => 'datetime',
            'read_at' => 'datetime',
        ];
    }
}
