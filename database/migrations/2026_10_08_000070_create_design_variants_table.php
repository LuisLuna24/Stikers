<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('design_variants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('design_id')->constrained()->cascadeOnDelete();
            $table->foreignId('sticker_size_id')->constrained()->cascadeOnDelete();
            $table->foreignId('sticker_finish_id')->constrained()->cascadeOnDelete();
            $table->decimal('unit_price', 10, 2);
            $table->unsignedInteger('minimum_quantity')->default(5);
            $table->unsignedInteger('quantity_increment')->default(5);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->unique(
                ['design_id', 'sticker_size_id', 'sticker_finish_id'],
                'design_variants_combination_unique',
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('design_variants');
    }
};
