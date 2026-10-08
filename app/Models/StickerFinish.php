<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['name', 'description', 'image_url', 'is_active'])]
class StickerFinish extends Model
{
    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
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
