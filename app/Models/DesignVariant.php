<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['design_id', 'sticker_size_id', 'sticker_finish_id', 'unit_price', 'minimum_quantity', 'quantity_increment', 'is_active'])]
class DesignVariant extends Model
{
    protected function casts(): array
    {
        return ['unit_price' => 'decimal:2', 'is_active' => 'boolean'];
    }

    public function design(): BelongsTo
    {
        return $this->belongsTo(Design::class);
    }

    public function stickerSize(): BelongsTo
    {
        return $this->belongsTo(StickerSize::class);
    }

    public function stickerFinish(): BelongsTo
    {
        return $this->belongsTo(StickerFinish::class);
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function acceptsQuantity(int $quantity): bool
    {
        return $quantity >= $this->minimum_quantity
            && ($quantity - $this->minimum_quantity) % $this->quantity_increment === 0;
    }
}
