<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('order_status_history', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->enum('from_status', ['pending_deposit', 'in_review', 'pending_customer_changes', 'awaiting_deposit', 'in_production', 'ready_for_pickup', 'delivered_balance_due', 'completed', 'cancelled', 'refunded'])->nullable();
            $table->enum('to_status', ['pending_deposit', 'in_review', 'pending_customer_changes', 'awaiting_deposit', 'in_production', 'ready_for_pickup', 'delivered_balance_due', 'completed', 'cancelled', 'refunded']);
            $table->foreignId('changed_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->text('note')->nullable();
            $table->timestamp('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_status_history');
    }
};
