<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['customer_id', 'tax_person_type', 'legal_name', 'rfc', 'fiscal_regime_code', 'fiscal_postal_code', 'cfdi_use_code', 'fiscal_email', 'is_default'])]
class TaxProfile extends Model
{
    protected function casts(): array
    {
        return ['is_default' => 'boolean'];
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(CustomerProfile::class, 'customer_id');
    }

    public function invoiceRequests(): HasMany
    {
        return $this->hasMany(OrderInvoiceRequest::class, 'tax_profile_id');
    }
}
