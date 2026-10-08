<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['name', 'width_cm', 'height_cm', 'is_active'])]
class StickerSize extends Model
{
    protected function casts(): array
    {
        return ['width_cm' => 'decimal:2', 'height_cm' => 'decimal:2', 'is_active' => 'boolean'];
    }

    public function variants(): HasMany
    {
        return $this->hasMany(DesignVariant::class);
    }

    public function designRequests(): HasMany
    {
        return $this->hasMany(DesignRequest::class, 'desired_size_id');
    }
}
