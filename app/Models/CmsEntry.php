<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class CmsEntry extends Model
{
    use SoftDeletes;

    protected $fillable = ['key', 'type', 'slug', 'sort_order', 'is_required'];

    protected function casts(): array
    {
        return [
            'is_required' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    public function contents(): HasMany
    {
        return $this->hasMany(CmsContent::class);
    }

    public function revisions(): HasMany
    {
        return $this->hasMany(CmsRevision::class)->latest('created_at')->latest('id');
    }

    /** The content row for a language (created empty when missing). */
    public function contentFor(string $locale): CmsContent
    {
        $content = $this->relationLoaded('contents')
            ? $this->contents->firstWhere('locale', $locale)
            : $this->contents()->where('locale', $locale)->first();

        return $content ?? new CmsContent(['cms_entry_id' => $this->id, 'locale' => $locale]);
    }
}
