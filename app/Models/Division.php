<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Division extends Model
{
    use \App\Traits\HasUniqueSlug;

    protected $guarded = [];

    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class);
    }

    public function assets(): HasMany
    {
        return $this->hasMany(Asset::class);
    }

    public function categories(): HasMany
    {
        return $this->hasMany(Category::class);
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }
}
