<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

#[Fillable(['design_id', 'name', 'description', 'is_active'])]
class StickerFinish extends Model implements HasMedia
{
    use InteractsWithMedia;

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }

    public function design(): BelongsTo
    {
        return $this->belongsTo(Design::class);
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('image')->singleFile();
    }

    public function variants(): HasMany
    {
        return $this->hasMany(DesignVariant::class);
    }

    public function designRequests(): HasMany
    {
        return $this->hasMany(DesignRequest::class, 'desired_finish_id');
    }
}
