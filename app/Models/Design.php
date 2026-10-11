<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['sku', 'name', 'slug', 'description', 'vector_file_url', 'preview_image_url', 'is_active'])]
class Design extends Model
{
    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }

    public function variants(): HasMany
    {
        return $this->hasMany(DesignVariant::class);
    }

    public function finishes(): HasMany
    {
        return $this->hasMany(StickerFinish::class);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }
}
