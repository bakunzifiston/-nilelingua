<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProjectEnquiry extends Model
{
    protected $fillable = [
        'name',
        'email',
        'organization',
        'service',
        'message',
    ];
}
