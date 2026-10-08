<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('design_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained('customer_profiles')->restrictOnDelete();
            $table->string('title', 180);
            $table->string('design_reference_url', 2048);
            $table->text('description')->nullable();
            $table->unsignedInteger('requested_quantity')->nullable();
            $table->foreignId('desired_size_id')->nullable()->constrained('sticker_sizes')->nullOnDelete();
            $table->foreignId('desired_finish_id')->nullable()->constrained('sticker_finishes')->nullOnDelete();
            $table->enum('status', ['submitted', 'reviewing', 'quoted', 'accepted', 'declined', 'converted_to_order', 'cancelled'])->default('submitted');
            $table->text('response_note')->nullable();
            $table->decimal('quoted_unit_price', 10, 2)->nullable();
            $table->timestamp('quoted_at')->nullable();
            $table->foreignId('converted_order_id')->nullable()->constrained('orders')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('design_requests');
    }
};
