<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Unit extends Model
{
    use \App\Traits\HasUniqueSlug;

    protected $guarded = [];

    public function divisions(): HasMany
    {
        return $this->hasMany(Division::class);
    }

    public function assets(): HasMany
    {
        return $this->hasMany(Asset::class);
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }
}
