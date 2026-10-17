<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CmsContent extends Model
{
    protected $fillable = ['cms_entry_id', 'locale', 'published', 'draft', 'published_at', 'draft_updated_at', 'updated_by'];

    protected function casts(): array
    {
        return [
            'published' => 'array',
            'draft' => 'array',
            'published_at' => 'datetime',
            'draft_updated_at' => 'datetime',
        ];
    }

    public function entry(): BelongsTo
    {
        return $this->belongsTo(CmsEntry::class, 'cms_entry_id');
    }

    public function editor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /** What the editor works on: the pending draft, or the live content when there is none. */
    public function working(): ?array
    {
        return $this->draft ?? $this->published;
    }

    public function isPublished(): bool
    {
        return $this->published !== null;
    }

    public function hasDraft(): bool
    {
        return $this->draft !== null;
    }
}
