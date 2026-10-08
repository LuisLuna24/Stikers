<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['order_id', 'design_variant_id', 'design_name_snapshot', 'size_snapshot', 'width_cm_snapshot', 'height_cm_snapshot', 'finish_snapshot', 'quantity', 'unit_price', 'line_total'])]
class OrderItem extends Model
{
    protected function casts(): array
    {
        return ['width_cm_snapshot' => 'decimal:2', 'height_cm_snapshot' => 'decimal:2', 'unit_price' => 'decimal:2', 'line_total' => 'decimal:2'];
    }

    public function order(): BelongsTo { return $this->belongsTo(Order::class); }
    public function designVariant(): BelongsTo { return $this->belongsTo(DesignVariant::class); }
}
