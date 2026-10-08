<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_number', 30)->unique();
            $table->foreignId('customer_id')->constrained('customer_profiles')->restrictOnDelete();
            $table->enum('status', ['pending_deposit', 'in_review', 'pending_customer_changes', 'awaiting_deposit', 'in_production', 'ready_for_pickup', 'delivered_balance_due', 'completed', 'cancelled', 'refunded'])->default('pending_deposit');
            $table->string('delivery_method', 30)->default('pickup');
            $table->text('pickup_location')->nullable();
            $table->date('pickup_date')->nullable();
            $table->time('pickup_time')->nullable();
            $table->foreignId('shipping_address_id')->nullable()->constrained('customer_addresses')->nullOnDelete();
            $table->timestamp('requested_at');
            $table->timestamp('confirmed_at')->nullable();
            $table->timestamp('production_started_at')->nullable();
            $table->unsignedInteger('estimated_production_days')->nullable();
            $table->date('estimated_ready_at')->nullable();
            $table->timestamp('delivered_at')->nullable();
            $table->decimal('subtotal', 12, 2)->default(0);
            $table->decimal('total_amount', 12, 2)->default(0);
            $table->decimal('deposit_percentage', 5, 2)->default(33);
            $table->decimal('deposit_required', 12, 2)->default(0);
            $table->decimal('amount_paid', 12, 2)->default(0);
            $table->decimal('balance_due', 12, 2)->default(0);
            $table->text('customer_note')->nullable();
            $table->text('internal_note')->nullable();
            $table->text('cancellation_reason')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
