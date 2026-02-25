<?php

namespace App\Traits;

use Illuminate\Support\Str;

trait HasUniqueSlug
{
    protected static function bootHasUniqueSlug()
    {
        static::creating(function ($model) {
            if (empty($model->slug) && !empty($model->name)) {
                $slug = Str::slug($model->name);
                $originalSlug = $slug;
                $count = 1;

                while (static::where('slug', $slug)->exists()) {
                    $slug = "{$originalSlug}-{$count}";
                    $count++;
                }

                $model->slug = $slug;
            }
        });

        static::updating(function ($model) {
            if ($model->isDirty('name') && empty($model->slug)) {
                $slug = Str::slug($model->name);
                $originalSlug = $slug;
                $count = 1;

                while (static::where('slug', $slug)->where('id', '!=', $model->id)->exists()) {
                    $slug = "{$originalSlug}-{$count}";
                    $count++;
                }

                $model->slug = $slug;
            }
        });
    }

    public function getRouteKeyName()
    {
        return 'slug';
    }
}
