<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['customer_id', 'title', 'design_reference_url', 'description', 'requested_quantity', 'desired_size_id', 'desired_finish_id', 'status', 'response_note', 'quoted_unit_price', 'quoted_at', 'converted_order_id'])]
class DesignRequest extends Model
{
    protected function casts(): array
    {
        return ['quoted_unit_price' => 'decimal:2', 'quoted_at' => 'datetime'];
    }

    public function customer(): BelongsTo { return $this->belongsTo(CustomerProfile::class, 'customer_id'); }
    public function desiredSize(): BelongsTo { return $this->belongsTo(StickerSize::class, 'desired_size_id'); }
    public function desiredFinish(): BelongsTo { return $this->belongsTo(StickerFinish::class, 'desired_finish_id'); }
    public function convertedOrder(): BelongsTo { return $this->belongsTo(Order::class, 'converted_order_id'); }
}
