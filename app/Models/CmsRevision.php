<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CmsRevision extends Model
{
    public const UPDATED_AT = null;

    protected $fillable = ['cms_entry_id', 'locale', 'content', 'user_id'];

    protected function casts(): array
    {
        return ['content' => 'array'];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
