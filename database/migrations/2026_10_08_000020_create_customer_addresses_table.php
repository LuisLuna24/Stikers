<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('customer_addresses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained('customer_profiles')->cascadeOnDelete();
            $table->string('label', 80)->nullable();
            $table->string('recipient_name', 150)->nullable();
            $table->string('phone', 30)->nullable();
            $table->string('street', 180);
            $table->string('exterior_number', 30)->nullable();
            $table->string('interior_number', 30)->nullable();
            $table->string('neighborhood', 120);
            $table->string('city', 120);
            $table->string('municipality', 120)->nullable();
            $table->string('state', 120);
            $table->string('postal_code', 10);
            $table->string('country', 80)->default('México');
            $table->text('references')->nullable();
            $table->boolean('is_default')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('customer_addresses');
    }
};
