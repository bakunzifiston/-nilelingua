<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CmsString extends Model
{
    protected $fillable = ['key_hash', 'key', 'en', 'fr', 'updated_by'];

    public static function hashFor(string $key): string
    {
        return sha1($key);
    }

    public function editor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
