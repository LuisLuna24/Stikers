<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['order_id', 'tax_profile_id', 'status', 'invoice_uuid', 'invoice_file_url', 'note'])]
class OrderInvoiceRequest extends Model
{
    public function order(): BelongsTo { return $this->belongsTo(Order::class); }
    public function taxProfile(): BelongsTo { return $this->belongsTo(TaxProfile::class); }
}
