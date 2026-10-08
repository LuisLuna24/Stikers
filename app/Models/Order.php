<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

#[Fillable(['order_number', 'customer_id', 'status', 'delivery_method', 'pickup_location', 'pickup_date', 'pickup_time', 'shipping_address_id', 'requested_at', 'confirmed_at', 'production_started_at', 'estimated_production_days', 'estimated_ready_at', 'delivered_at', 'subtotal', 'total_amount', 'deposit_percentage', 'deposit_required', 'amount_paid', 'balance_due', 'customer_note', 'internal_note', 'cancellation_reason'])]
class Order extends Model
{
    protected function casts(): array
    {
        return [
            'pickup_date' => 'date',
            'requested_at' => 'datetime',
            'confirmed_at' => 'datetime',
            'production_started_at' => 'datetime',
            'estimated_ready_at' => 'date',
            'delivered_at' => 'datetime',
            'subtotal' => 'decimal:2',
            'total_amount' => 'decimal:2',
            'deposit_percentage' => 'decimal:2',
            'deposit_required' => 'decimal:2',
            'amount_paid' => 'decimal:2',
            'balance_due' => 'decimal:2',
        ];
    }

    public function customer(): BelongsTo { return $this->belongsTo(CustomerProfile::class, 'customer_id'); }
    public function shippingAddress(): BelongsTo { return $this->belongsTo(CustomerAddress::class, 'shipping_address_id'); }
    public function items(): HasMany { return $this->hasMany(OrderItem::class); }
    public function statusHistory(): HasMany { return $this->hasMany(OrderStatusHistory::class); }
    public function payments(): HasMany { return $this->hasMany(Payment::class); }
    public function designRequests(): HasMany { return $this->hasMany(DesignRequest::class, 'converted_order_id'); }
    public function invoiceRequest(): HasOne { return $this->hasOne(OrderInvoiceRequest::class); }
}
