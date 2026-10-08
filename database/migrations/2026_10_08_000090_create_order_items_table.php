<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('design_variant_id')->constrained()->restrictOnDelete();
            $table->string('design_name_snapshot', 180);
            $table->string('size_snapshot', 80);
            $table->decimal('width_cm_snapshot', 8, 2);
            $table->decimal('height_cm_snapshot', 8, 2);
            $table->string('finish_snapshot', 100);
            $table->unsignedInteger('quantity');
            $table->decimal('unit_price', 10, 2);
            $table->decimal('line_total', 12, 2);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_items');
    }
};
